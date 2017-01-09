<?php namespace Credibility\DandB;

use Credibility\DandB\Models;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use Credibility\DandB\Cache\CacheableInterface;
use LogicException;

class DandB {

    /** @var Requester */
    protected $requester;

    public function __construct(Requester $requester)
    {
        $this->requester = $requester;
    }

    /**
     * Factory method to create DandB object
     * @param string $baseUrl
     * @param $clientId
     * @param $clientSecret
     * @param array $guzzleOpts
     * @return DandB
     */
    public static function getInstance(
        $clientId, $clientSecret,
        $baseUrl = 'https://api.dandb.com',
        $guzzleOpts = array(),
        CacheableInterface $cache = null,
        $accessToken = null
    )
    {
        $clientFactory = new ClientFactory($baseUrl, $guzzleOpts);
        $requester = new Requester($clientFactory, $clientId, $clientSecret, $cache, $accessToken);
        return new DandB($requester);
    }

    /**
     * Returns an access token if available, otherwise false.
     * Note: tokens last for 1 hour. It's up to the client to
     * cache this token if needed
     *
     * @return bool|string
     * @throws RequestException|LogicException|ParseException
     */
    public function getAccessToken()
    {
        return $this->requester->getAccessToken();
    }

    /**
     * Given a DUNS, run an international search
     *
     * Returns an array of results or false if an error occurred
     *
     * @param $duns
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function internationalSearchByDuns($duns)
    {
        return $this->requester->runGet('/v1/business/search/international', array(
            'duns' => $duns
        ));
    }

    /**
     * Given a Business Name and a country, run an international search
     *
     * Returns an array of results or false if an error occurred
     *
     * @param $name
     * @param $country
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function internationalSearchByNameCountry($name, $country)
    {
        return $this->requester->runGet('/v1/business/search/international', array(
            'name' => $name,
            'country' => $country,
        ));
    }

    /**
     * Searches businesses based on DUNS Number
     *
     * @param $duns
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByDuns($duns)
    {
        return $this->requester->runGet('/v1/business/search', array(
            'duns' => $duns,
        ));
    }

    /**
     * Searches businesses by name and address
     *
     * @param $name
     * @param $state
     * @param null $address
     * @param null $city
     * @param null $zip
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByNameAddress($name, $state, $address = null, $city = null, $zip = null)
    {
        $array = array(
            'name' => $name,
            'state' => $state,
        );

        if(!is_null($address)) { $array['address'] = $address; }
        if(!is_null($city)) { $array['city'] = $city; }
        if(!is_null($zip)) { $array['zip'] = $zip; }

        return $this->requester->runGet('/v1/business/search', $array);
    }

    /**
     * Gets a page from the CMS
     *
     * @param string $page
     * @param string $language - valid params: 'en', 'fr'
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */

    public function getPageFromDandBCMS($pageName, $language = 'en') {
        return $this->requester->runGet('/v1/content/' . $pageName . '/' . $language);
    }

    /**
     * Searches businesses by phone number
     *
     * @param $phone
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByPhone($phone)
    {
        return $this->requester->runGet('/v1/business/search', array(
            'phone' => $phone
        ));
    }

    /**
     * Returns Verified information based on D&B Enterprise Business ID
     *
     * @param $businessId
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfile($businessId)
    {
        return $this->requester->runGet("/v2/verified/$businessId", array());
    }

    /**
     * Returns Verified information based on DUNS Number
     *
     * @param $duns
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfileWithDuns($duns)
    {
        return $this->requester->runGet("/v1/verified/$duns", array(
           'duns' => true
        ));
    }

    /**
     * Returns a User Token for an existing user
     *
     * @param $email
     * @param $password
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userToken($email, $password)
    {
        return $this->requester->runPost('/v1/user/token', array(
            'email' => $email,
            'password' => $password
        ));
    }

    /**
     * Logout a user from OWL
     * @return Response
     */
    public function userLogout($userToken)
    {
        return $this->requester->runPost('/v1/user/logout', array(
            'user_token' => $userToken
        ));
    }

    /**
     * Returns User based on User Token
     *
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userUsingToken($userToken)
    {
        return $this->requester->runGet('/v1/user/token/status', array(
            'user_token' => $userToken
        ));
    }

    /**
     * Returns User based on User Token
     *
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userFullDetails($userToken)
    {
        return $this->requester->runGet('/v1.1/user', array(
            'user_token' => $userToken
        ));
    }

    /**
     * Send out an email from owl to reset the password
     * @param $email
     * @return Response
     */
    public function passwordReset($email)
    {
        return $this->requester->runPost('/v1/user/password/reset', array(
            'email' => $email
        ));
    }

    /**
     * Allow a user to change their password
     * @param  $userToken
     * @param  $oldPassword
     * @param  $newPassword
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function passwordChange($userToken, $oldPassword, $newPassword)
    {
        return $this->requester->runPost('/v1/user/password/change', array(
            'user_token' => $userToken,
            'old_password' => $oldPassword,
            'new_password' => $newPassword
        ));
    }

    /**
     * Allow a user to register a new account
     * @param  $email
     * @param  $firstName
     * @param  $lastName
     * @param  $phone
     * @param  $password
     * @param  $acceptedTOS must be 1
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userRegister($email, $first_name, $last_name, $accepted_tos, $password=null, $phone_number=null, $address_line_1=null, $address_line_2=null, $address_line_3=null, $city=null, $state_code=null, $postal_code=null, $source=null)
    {
        //required
        $params = compact('email','first_name','last_name','accepted_tos');
        //optional (only add to params if not empty)
        $optionalParams = array('password','phone_number','address_line_1','address_line_2','address_line_3','city','state_code','postal_code','source');
        foreach($optionalParams as $optional) {
            if(!empty($$optional)) {
                $params[$optional] = $$optional;
            }
        }

        return $this->requester->runPost('/v1.1/user/register', $params);
    }


    /**
     * Accept TOS
     * @param string|null $userToken
     * @param string|null $email
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userAccceptTOS($userToken = null, $email = null) {
        if (empty($userToken) && empty($userToken)) {
            throw new \InvalidArgumentException('You must provide either a user token or an email address');
        }
        $data = !empty($userToken) ? array('user_token' => $userToken) : array('email' => $email);
        return $this->requester->runPost('/v1/user/accept-tos', $data);
    }

    /**
     * Returns User Entitlements based on User Token
     *
     * @see DandB::userToken
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userEntitlements($userToken)
    {
        return $this->requester->runGet('/v1.1/user/entitlements', array(
            'user_token' => $userToken
        ));
    }


    /**
     * Entitle multiple products to an existing user
     *
     * @param string $userToken
     * @param DandbOrder $order
     * @returns Response
     * @throws RequestException|LogicException|ParseException
     */
    public function addUserEntitlements($userToken, Models\DandbOrder $order) {
        $agent = $order->hasAgent() ? $order->getAgent() : new Models\DandbAgent();

        return $this->requester->runPost('/v1.1/user/entitlements', array(
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
            'customer_group_domain_code' => $order->getCustomerGroupDomainCode(),
        ));
    }

    /**
     * Returns Product Recommendations based on DUNS Number
     * Manhattan Project
     *
     * @param $duns
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function getProductRecommendations($duns) {
        return $this->requester->runGet("/v1/business/$duns/recommended-products");
    }

    /**
     * Entitle single product to an existing user
     *
     * @param string $userToken
     * @param DandBOrder $order
     * @returns Response
     * @throws RequestException|LogicException|ParseException
     */
    public function addSingleProductUserEntitlement($userToken, Models\DandBOrder $order) {
        $agent = $order->hasAgent() ? $order->getAgent() : new Models\DandBAgent();
        $product = $order->getFirstProduct() ? $order->getFirstProduct() : new Models\DandBProduct();

        return $this->requester->runPost('/v1.1/user/entitlement', array(
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
        ));
    }

    /**
     * @param $userEmail    User email address of the recipient of.
     * @param $displayName  Name of the recipient of the email.
     * @param $folderName   The Responsys folder of this email.
     * @param $campaignName The Responsys campaign name of this email.
     * @param $messageId    Message identifier used for tracking this email.
     * @param $options      array of data required by the Responsys template.
     * @return Response
     */
    public function postEmail($userEmail, $displayName, $folderName, $campaignName, $messageId, array $options) {
        return $this->requester->runPost('/v1.1/email', array(
            'user_email' => $userEmail,
            'display_name' => $displayName,
            'folder_name' => $folderName,
            'campaign_name' => $campaignName,
            'message_id' => $messageId,
            'options' => json_encode($options),
        ));
    }

    /**
     * Returns new user token based on refresh token from
     * the User Token call
     *
     * @see DandB::userToken
     * @param $email
     * @param $refreshToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userTokenRefresh($email, $refreshToken)
    {
        return $this->requester->runPost('/v1/user/token/refresh', array(
            'email' => $email,
            'refresh_token' => $refreshToken
        ));
    }

    /**
     * Return the status of a user based on a user token
     *
     * @see DandB::userToken
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userTokenStatus($userToken)
    {
        return $this->requester->runGet('/v1/user/token', array(
            'user_token' => $userToken
        ));
    }

    /**
     * Return the user token from OAuth2 auth code
     *
     * @see DandB::authCode
     * @param $authCode
     * @return Response
     */
    public function getUserTokenFromAuthCode($authCode)
    {
        return $this->requester->runJsonPost('/v1/oauth2/token/authorization_code', array(
            'code' => $authCode
        ));
    }

    /**
     * Return the auth code appended in the redirect url by providing the user token
     * @param $clientId
     * @param $redirectUrl
     * @param $state
     * @return Response
     */
    public function getAuthCodeFromUserToken($clientId, $redirectUrl, $state)
    {
        return $this->requester->runJsonPost('/v1/oauth2/authorize/code', array(
            'client_id' => $clientId,
            "redirect_uri" => $redirectUrl,
            "state" => $state
        ));
    }

}
