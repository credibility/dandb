<?php namespace Credibility\DandB;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class RequesterTest extends PHPUnit_Framework_TestCase {

    protected $clientId = 'test-client';
    protected $clientSecret = 'test-secret';

    /** @var MockInterface */
    protected $mockGuzzle;
    protected $mockClientFactory;
    /** @var Requester */
    protected $requester;

    const MOCK_ACCESS_TOKEN = 'mock-access-token';

    public function setUp()
    {
        $this->mockGuzzle = m::mock('GuzzleHttp\Client');
        $this->mockClientFactory = m::mock('Credibility\DandB\ClientFactory');

        $this->mockClientFactory->shouldReceive('createClient')
            ->andReturn($this->mockGuzzle);

        $this->requester = new Requester($this->mockClientFactory, $this->clientId, $this->clientSecret);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetAccessTokenSuccess()
    {
        $mockResponse = $this->getMockAccessTokenResponse(
            array('access_token' => 'test-token')
        );

        $this->mockGuzzle->shouldReceive('post')
            ->with('/v1/oauth/token', m::any())
            ->andReturn($mockResponse);

        $accessToken = $this->requester->getAccessToken();

        $this->assertEquals('test-token', $accessToken);
    }

    public function testGetAccessTokenError()
    {
        $mockResponse = $this->getMockAccessTokenResponse(array());

        $this->mockGuzzle->shouldReceive('post')
            ->with('/v1/oauth/token', m::any())
            ->andReturn($mockResponse);

        $shouldBeFalse = $this->requester->getAccessToken();

        $this->assertFalse($shouldBeFalse);
    }

    public function testCreateRequestParams()
    {
        $data = array(
            'test' => 'value'
        );

        $params = $this->requester->createRequestParams($data, 'test-token');

        $this->assertArrayHasKey('headers', $params);
        $this->assertEquals('test-token', $params['headers']['x-access-token']);

        $this->assertArrayHasKey('test', $params);
        $this->assertEquals('value', $params['test']);
    }

    public function testCreateRequestParamsForEmptyArray()
    {
        $params = $this->requester->createRequestParams(array(), 'test-token');

        $this->assertArrayHasKey('headers', $params);
        $this->assertEquals('test-token', $params['headers']['x-access-token']);
    }

    public function testRunGetWithAccessToken()
    {
        $testArray = $this->setHttpMethodExpectations();
        $mockAccessToken = 'test-token';

        $response = $this->requester->runGet('test-uri', $testArray, $mockAccessToken);

        $this->assertInstanceOf('Credibility\DandB\Response', $response);
        $this->assertTrue($response->isValid());
        $this->assertArrayHasKey('business_name', $response->getResponseData());
    }

    public function testRunGetWithoutAccessToken()
    {
        $testArray = $this->setHttpMethodExpectations();
        $response = $this->requester->runGet('test-uri', $testArray);

        $this->assertInstanceOf('Credibility\DandB\Response', $response);
        $this->assertTrue($response->isValid());
        $this->assertArrayHasKey('business_name', $response->getResponseData());
        $this->assertArrayHasKey('business_value', $response->getResponseData());
    }

    public function testRunPostWithAccessToken()
    {
        $testArray = $this->setHttpMethodExpectations();
        $mockAccessToken = 'test-token';

        $response = $this->requester->runPost('test-uri', $testArray, $mockAccessToken);

        $this->assertInstanceOf('Credibility\DandB\Response', $response);
        $this->assertTrue($response->isValid());
        $this->assertArrayHasKey('business_name', $response->getResponseData());
    }

    public function testRunPostWithoutAccessToken()
    {
        $testArray = $this->setHttpMethodExpectations();

        $response = $this->requester->runPost('test-uri', $testArray);

        $this->assertInstanceOf('Credibility\DandB\Response', $response);
        $this->assertTrue($response->isValid());
        $this->assertArrayHasKey('business_name', $response->getResponseData());
        $this->assertArrayHasKey('business_value', $response->getResponseData());
    }

    protected function getMockResponseInterface($data)
    {
        $mockResponse = m::mock('GuzzleHttp\Message\ResponseInterface');

        $mockResponse->shouldReceive('json')
            ->andReturn($data);

        return $mockResponse;
    }

    protected function getMockRequestInterface()
    {
        $mockRequest = m::mock('GuzzleHttp\Message\RequestInterface');

        return $mockRequest;
    }

    protected function setMockAccessTokenCall()
    {
        $mockAccessTokenResponse = $this->getMockAccessTokenResponse(array(
            'access_token' => self::MOCK_ACCESS_TOKEN
        ));

        $this->mockGuzzle->shouldReceive('post')
            ->with('/v1/oauth/token', m::any())
            ->andReturn($mockAccessTokenResponse);
    }

    protected function getMockAccessTokenResponse($data)
    {
        $mockResponse = m::mock('GuzzleHttp\Message\ResponseInterface');

        $mockResponse->shouldReceive('json')
            ->andReturn($data);

        return $mockResponse;
    }

    private function setHttpMethodExpectations()
    {
        $testArray = array(
            'meta' => array(
                'code' => 200
            ),
            'response' => array(
                'results' => array(
                    'business_name' => 'test',
                    'business_value' => 'something'
                )
            )
        );
        $this->setMockAccessTokenCall();
        $mockResponse = $this->getMockResponseInterface($testArray);
        $mockRequest = $this->getMockRequestInterface();

        $this->mockGuzzle->shouldReceive('createRequest')
            ->andReturn($mockRequest);

        $this->mockGuzzle->shouldReceive('send')
            ->with($mockRequest)
            ->andReturn($mockResponse);
        return $testArray;
    }
}
 