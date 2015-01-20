<?php namespace Credibility\DandB;

use Credibility\DandB\Models;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class DandBTest extends PHPUnit_Framework_TestCase
{

    /** @var DandB */
    protected $dandb;

    /** @var MockInterface */
    protected $mockRequester;

    private $testAccessToken;

    public function setUp()
    {
        $this->mockRequester = m::mock('Credibility\DandB\Requester');

        $this->dandb = new DandB($this->mockRequester);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetAccessToken()
    {
        $this->mockRequester
            ->shouldReceive('getAccessToken')
            ->withNoArgs()->once();

        $this->dandb->getAccessToken();
    }

    public function testBusinessSearchByPhone() {
        $phone = '3235551234';

        $this->setMockRequesterExpectations('runGet',
            "/v1/business/search", array(
                'phone' => $phone
            )
        );

        $this->dandb->businessSearchByPhone($phone);
    }

    public function testVerifiedProfile()
    {
        $businessId = '123';

        $this->setMockRequesterExpectations('runGet',
            "/v2/verified/$businessId", array()
        );

        $this->dandb->verifiedProfile($businessId);
    }

    public function testVerifiedProfileWithDuns()
    {
        $businessDuns = '007280554';

        $this->setMockRequesterExpectations('runGet',
            "/v1/verified/$businessDuns", array('duns' => true)
        );

        $this->dandb->verifiedProfileWithDuns($businessDuns);
    }

    public function testUserToken()
    {
        $email = 'test@yopmail.com';
        $password = 'Pass@123';

        $this->setMockRequesterExpectations('runPost',
            '/v1/user/token', array(
                'email' => $email,
                'password' => $password
            )
        );

        $this->dandb->userToken($email, $password);
    }

    public function testUserLogout()
    {
        $userToken = 'abcde123';

        $this->setMockRequesterExpectations('runPost',
            '/v1/user/logout', array(
                'user_token' => $userToken
            )
        );

        $this->dandb->userLogout($userToken);
    }

    public function testUserUsingToken()
    {
        $userToken = 'abcde123';

        $this->setMockRequesterExpectations('runGet',
            '/v1/user/token/status', array(
                'user_token' => $userToken,
            )
        );

        $this->dandb->userUsingToken($userToken);
    }

    public function testUserFullDetails()
    {
        $userToken = 'abcde123';

        $this->setMockRequesterExpectations('runGet',
            '/v1.1/user', array(
                'user_token' => $userToken,
            )
        );

        $this->dandb->userFullDetails($userToken);
    }

    public function testPasswordReset()
    {
        $email = 'test@yopmail.com';

        $this->setMockRequesterExpectations('runPost',
            '/v1/user/password/reset', array(
                'email' => $email,
            )
        );

        $this->dandb->passwordReset($email);
    }

    public function testPasswordChange()
    {
        $userToken = 'abcde123';
        $oldPassword = 'Pass@123';
        $newPassword = 'Test@321';

        $this->setMockRequesterExpectations('runPost',
            '/v1/user/password/change', array(
                'user_token' => $userToken,
                'old_password' => $oldPassword,
                'new_password' => $newPassword
            )
        );

        $this->dandb->passwordChange($userToken, $oldPassword, $newPassword);
    }

    public function testUserRegister()
    {
        $email = 'test@yopmail.com';
        $firstName = 'Test';
        $lastName = 'McTester';
        $phone = '3235551234';
        $password = 'Pass@123';
        $acceptedTOS = 1;

        $this->setMockRequesterExpectations('runPost',
            '/v1.1/user/register', array(
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone_number' => $phone,
                'password' => $password,
                'accepted_tos' => $acceptedTOS
            )
        );

        $this->dandb->userRegister($email, $firstName, $lastName, $acceptedTOS, $password, $phone);
    }

    public function testUserEntitlements()
    {
        $userToken = 'abcde123';

        $this->setMockRequesterExpectations('runGet',
            '/v1.1/user/entitlements', array(
                'user_token' => $userToken
            )
        );

        $this->dandb->userEntitlements($userToken);
    }

    public function testAddUserEntitlements()
    {
        $userToken = 'abcde123';
        $order = new Models\DandBOrder();
        $order->addProduct((new Models\DandBProduct())->setProductId('1')->setPriceId('2'));
        $agent = new Models\DandBAgent();

        $this->setMockRequesterExpectations('runPost',
            '/v1.1/user/entitlements', array(
                'user_token' => $userToken,
                'payment_type' => $order->getPaymentType(),
                'orders' => json_encode($order->getProductsArray()),
                'agent_identifier' => $agent->getAgentId(),
                'agent_office_code' => $agent->getAgentOfficeCode(),
                'assigned_agent_code' => $agent->getAssignedAgentCode(),
                'partner_identifier' => $order->getPartnerIdentifier(),
                'order_level_promotion_identifier' => $order->getOrderLevelPromotionIdentifier(),
                'case_reference_identifier' => $order->getCaseLevelIdentifier(),
                'five9_session_identifier' => $order->getFive9SessionIdentifier(),
                'order_payment_type_code' => $order->getPaymentTypeCode(),
                'customer_group_domain_code' => $order->getCustomerGroupDomainCode()
            )
        );

        $this->dandb->addUserEntitlements($userToken, $order);
    }

    public function testAddSingleProductUserEntitlement()
    {
        $userToken = 'abcde123';
        $order = (new Models\DandBOrder())
            ->setPaymentTypeCode('FREE')
            ->setOrderLevelPromotionIdentifier(1)
            ->setFive9SessionIdentifier(1)
            ->setCustomerGroupDomainCode(1)
            ->setCaseLevelIdentifier(1)
            ->setPaymentType('FREE')
            ->setPartnerIdentifier(1)
            ->setSendConfirmationEmail(false);

        $order->addProduct(
            (new Models\DandBProduct())
                ->setProductId('1')
                ->setPriceId('2')
                ->setQuantity(1)
                ->setPromotionIdentifier(null)
                ->setPaymentSubTypeCode('FREE')
                ->setPaymentInstrumentIdentifier('FREE')
        );
        $product = $order->getFirstProduct();
        $agent = (new Models\DandBAgent())
                    ->setAssignedAgentCode(1)
                    ->setAgentOfficeCode(1)
                    ->setAgentId(1);

        $order->setAgent($agent);

        $this->setMockRequesterExpectations('runPost',
            '/v1.1/user/entitlement', array(
                'user_token' => $userToken,
                'payment_type' => $order->getPaymentType(),
                'send_confirmation_email' => $order->getSendConfirmationEmail(),
                'product_id' => $product->getProductId(),
                'price_id' => $product->getPriceId(),
                'quantity' => $product->getQuantity(),
                'duns' => $product->getDuns(),
                'promotion_identifier' => $product->getPromotionIdentifier(),
                'payment_sub_type_code' => $product->getPaymentSubTypeCode(),
                'payment_instrument_identifier' => $product->getPaymentInstrumentIdentifier(),
                'agent_identifier' => $agent->getAgentId(),
                'agent_office_code' => $agent->getAgentOfficeCode(),
                'assigned_agent_code' => $agent->getAssignedAgentCode(),
                'partner_identifier' => $order->getPartnerIdentifier(),
                'order_level_promotion_identifier' => $order->getOrderLevelPromotionIdentifier(),
                'case_reference_identifier' => $order->getCaseLevelIdentifier(),
                'five9_session_identifier' => $order->getFive9SessionIdentifier(),
                'order_payment_type_code' => $order->getPaymentTypeCode(),
                'customer_group_domain_code' => $order->getCustomerGroupDomainCode(),
            )
        );

        $this->dandb->addSingleProductUserEntitlement($userToken, $order);
    }

    public function testUserTokenRefresh()
    {
        $email = 'test@yopmail.com';
        $refreshToken = 'abcde123';

        $this->setMockRequesterExpectations('runPost',
            '/v1/user/token/refresh', array(
                'email' => $email,
                'refresh_token' => $refreshToken
            )
        );

        $this->dandb->userTokenRefresh($email, $refreshToken);
    }

    public function testUserTokenStatus()
    {
        $userToken = 'abcde123';

        $this->setMockRequesterExpectations('runGet',
            '/v1/user/token', array(
                'user_token' => $userToken
            )
        );

        $this->dandb->userTokenStatus($userToken);
    }

    public function testInternationalSearch()
    {
        $duns = '999999999';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search/international', array(
                'duns' => $duns
            )
        );

        $this->dandb->internationalSearchByDuns($duns);
    }

    public function testInternationalSearchByNameCountry()
    {
        $name = 'Test';
        $country = 'USA';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search/international', array(
                'name' => $name,
                'country' => $country
            )
        );

        $this->dandb->internationalSearchByNameCountry($name, $country);
    }

    public function testBusinessSearchByDuns()
    {
        $duns = '999999999';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search', array(
                'duns' => $duns
            )
        );

        $this->dandb->businessSearchByDuns($duns);
    }

    public function testBusinessSearchByNameAddressWithCoreParams()
    {
        $name = 'Test';
        $state = 'CA';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search', array(
                'name' => $name,
                'state' => $state
            )
        );

        $this->dandb->businessSearchByNameAddress($name, $state);
    }

    public function testBusinessSearchByNameAddressWithAddress()
    {
        $name = 'Test';
        $state = 'CA';
        $address = '123 Main St.';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search', array(
                'name' => $name,
                'state' => $state,
                'address' => $address
            )
        );

        $this->dandb->businessSearchByNameAddress($name, $state, $address);
    }

    public function testBusinessSearchByNameAddressWithCity()
    {
        $name = 'Test';
        $state = 'CA';
        $city = 'Los Angeles';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search', array(
                'name' => $name,
                'state' => $state,
                'city' => $city
            )
        );

        $this->dandb->businessSearchByNameAddress($name, $state, null, $city);
    }

    public function testBusinessSearchByNameAddressWithZip()
    {
        $name = 'Test';
        $state = 'CA';
        $city = 'Los Angeles';

        $this->setMockRequesterExpectations('runGet',
            '/v1/business/search', array(
                'name' => $name,
                'state' => $state,
                'city' => $city
            )
        );

        $this->dandb->businessSearchByNameAddress($name, $state, null, $city);
    }
    private function setMockRequesterExpectations(
        $mockRequesterRequest,
        $owlEndpoint,
        array $args
    ) {
        $this->mockRequester->shouldReceive($mockRequesterRequest)
            ->once()->withArgs([$owlEndpoint, $args]);
    }

}
