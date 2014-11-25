<?php namespace Credibility\DandB;

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
