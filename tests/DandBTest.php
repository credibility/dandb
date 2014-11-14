<?php namespace Credibility\DandB;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class DandBTest extends PHPUnit_Framework_TestCase {

    /** @var DandB */
    protected $dandb;

    /** @var MockInterface */
    protected $mockRequester;

    private $testAccessToken;
    public function setUp()
    {
        $this->mockRequester = m::mock('Credibility\DandB\Requester');
        $this->dandb = new DandB('test-client','test-secret', $this->mockRequester);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetInstance()
    {
        $dandb = DandB::getInstance('test-client', 'test-secret');
        $this->assertInstanceOf('Credibility\DandB\DandB', $dandb);
    }

    public function testGetAccessToken()
    {
        $this->mockRequester->shouldReceive('getAccessToken')
            ->once();

        $this->dandb->getAccessToken();

    }}
