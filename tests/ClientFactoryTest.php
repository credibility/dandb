<?php namespace Credibility\DandB;

use PHPUnit_Framework_TestCase;

class ClientFactoryTest extends PHPUnit_Framework_TestCase {

    /** @var ClientFactory */
    protected $clientFactory;

    protected $url = 'https://api-qa.malibucoding.com';

    protected $opts = array(
            'timeout' => 30,
            'connect_timeout' => 30
        );

    public function setUp()
    {
        $this->clientFactory = new ClientFactory($this->url, $this->opts);
    }

    public function testConstructHasCorrectData()
    {
        $this->assertEquals($this->url, $this->clientFactory->getBaseUrl());
        $this->assertTrue($this->opts == $this->clientFactory->getGuzzleOpts());
    }

    public function testCreateClient()
    {
        $client = $this->clientFactory->createClient();

        $this->assertInstanceOf('GuzzleHttp\Client', $client);
        $this->assertEquals($this->url, $client->getBaseUrl());
    }
}
 