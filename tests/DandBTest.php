<?php namespace Credibility\DandB;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class DandBTest extends PHPUnit_Framework_TestCase {

    /** @var DandB */
    protected $dandb;

    /** @var MockInterface */
    protected $mockRequester;

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
        $this->mockRequester->shouldReceive('getAccessToken')
            ->once();

        $this->dandb->getAccessToken();
    }

    public function testInternationalSearchByDuns()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/business/search/international',
            array('query' => array('duns' => 'test-duns')),
            null
        )->once();

        $this->dandb->internationalSearchByDuns('test-duns');
    }

    public function testInternationalSearchByNameCountry()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/business/search/international',
            array('query' => array('name' => 'test-name', 'country' => 'test-country')),
            null
        )->once();

        $this->dandb->internationalSearchByNameCountry('test-name', 'test-country');
    }

    public function testBusinessSearchByNameAddressMinimal()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/business/search',
            array('query' => array('name' => 'test-name', 'state' => 'test-state')),
            null
        )->once();

        $this->dandb->businessSearchByNameAddress('test-name', 'test-state');
    }

    public function testBusinessSearchByNameAddressFull()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/business/search',
            array('query' => array(
                'name' => 'test-name',
                'state' => 'test-state',
                'address' => 'test-address',
                'city' => 'test-city',
                'zip' => 'test-zip',
            )),
            null
        )->once();

        $this->dandb->businessSearchByNameAddress(
            'test-name',
            'test-state',
            'test-address',
            'test-city',
            'test-zip'
        );
    }

    public function testBusinessSearchByDuns()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/business/search',
            array('query' => array('duns' => 'test-duns')),
            null
        )->once();

        $this->dandb->businessSearchByDuns('test-duns');
    }

    public function testBusinessSearchByPhone()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/business/search',
            array('query' => array('phone' => '2222222222')),
            null
        )->once();

        $this->dandb->businessSearchByPhone('2222222222');
    }

    public function testVerifiedProfile()
    {
        $id = '123';

        $this->mockRequester->shouldReceive('runGet')->with(
            "/v1/verified/$id",
            array(),
            null
        )->once();

        $this->dandb->verifiedProfile($id);
    }

    public function testVerifiedProfileWithDuns()
    {
        $duns = 'test-duns';

        $this->mockRequester->shouldReceive('runGet')->with(
            "/v1/verified/$duns",
            array('query' => array('duns' => true)),
            null
        )->once();

        $this->dandb->verifiedProfileWithDuns($duns);
    }

    public function testUserToken()
    {
        $this->mockRequester->shouldReceive('runPost')->with(
            '/v1/user/token',
            array('body' => array('email' => 'test@test.com', 'password' => 'test-pass')),
            null
        )->once();

        $this->dandb->userToken('test@test.com', 'test-pass');
    }

    public function testUserEntitlements()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1.1/user/entitlements',
            array('query' => array('user_token' => 'test-user-token')),
            null
        )->once();

        $this->dandb->userEntitlements('test-user-token');
    }

    public function testUserTokenRefresh()
    {
        $this->mockRequester->shouldReceive('runPost')->with(
            '/v1/user/token/refresh',
            array('body' => array('email' => 'test@test.com', 'refresh_token' => 'test-refresh')),
            'access_token'
        )->once();

        $this->dandb->userTokenRefresh('test@test.com', 'test-refresh', 'access_token');
    }

    public function testUserTokenStatus()
    {
        $this->mockRequester->shouldReceive('runGet')->with(
            '/v1/user/token',
            array('query' => array('user_token' => 'test-user')),
            null
        )->once();

        $this->dandb->userTokenStatus('test-user');
    }

}
