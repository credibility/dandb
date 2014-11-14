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
    }

    public function tearDown()
    {
        m::close();
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }

}
