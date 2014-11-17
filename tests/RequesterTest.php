<?php namespace Credibility\DandB;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class RequesterTest extends PHPUnit_Framework_TestCase {

    protected $clientId = 'test-client';
    protected $clientSecret = 'test-secret';

    /** @var MockInterface */
    protected $mockGuzzle;
    /** @var MockInterface */
    protected $mockClientFactory;
    /** @var MockInterface */
    protected $mockCache;

    /** @var Requester */
    protected $requester;

    const MOCK_ACCESS_TOKEN = 'mock-access-token';

    public function setUp()
    {
        $this->mockGuzzle = m::mock('GuzzleHttp\Client');
        $this->mockClientFactory = m::mock('Credibility\DandB\ClientFactory');
        $this->mockCache = m::mock('Credibility\DandB\Cache\CacheableInterface');

        $this->mockClientFactory->shouldReceive('createClient')
            ->andReturn($this->mockGuzzle);

        $this->requester = new Requester(
            $this->mockClientFactory,
            $this->clientId,
            $this->clientSecret,
            $this->mockCache,
            self::MOCK_ACCESS_TOKEN
        );
    }

    public function tearDown()
    {
        m::close();
    }

    public function testPostAccessTokenSuccess()
    {
        $mockResponse = $this->getMockAccessTokenResponse(array(
            'access_token' => self::MOCK_ACCESS_TOKEN
        ));

        $this->mockGuzzle->shouldReceive('post')
            ->with('/v1/oauth/token', m::any())
            ->andReturn($mockResponse);

        $accessToken = $this->requester->postAccessToken();

        $this->assertEquals(self::MOCK_ACCESS_TOKEN, $accessToken);
    }

    public function testPostAccessTokenReturnsFalse()
    {
        $mockResponse = $this->getMockAccessTokenResponse([]);

        $this->mockGuzzle->shouldReceive('post')
            ->with('/v1/oauth/token', m::any())
            ->andReturn($mockResponse);

        $this->assertFalse($this->requester->postAccessToken());
    }

    public function testGetAccessTokenWhenSetInConstructor()
    {
        $requester = new Requester(
            $this->mockClientFactory,
            $this->clientId,
            $this->clientSecret,
            $this->mockCache,
            'test-123'
        );

        $this->assertEquals('test-123', $requester->getAccessToken());
    }

    public function testCacheExistsSuccess()
    {
        $this->assertTrue($this->requester->cacheExists());
    }

    public function testCacheExistsFails()
    {
        $testRequester = new Requester($this->mockClientFactory, $this->clientId, $this->clientSecret);

        $this->assertFalse($testRequester->cacheExists());
    }

    public function testGetAccessTokenWhenCached()
    {
        $requester = new Requester(
            $this->mockClientFactory,
            $this->clientId, $this->clientSecret,
            $this->mockCache, null
        );

        $this->setCacheExpectations(true, 'test-123');

        $this->assertEquals('test-123', $requester->getAccessToken());
    }

    public function testAccessTokenSetOnCacheMiss()
    {
        $requester = new Requester(
            $this->mockClientFactory,
            $this->clientId, $this->clientSecret,
            $this->mockCache, null
        );

        $this->mockCache->shouldReceive('has')
            ->once()->andReturn(false);

        $this->mockCache->shouldReceive('put')->once()
            ->with(Requester::ACCESS_TOKEN_CACHE_KEY, self::MOCK_ACCESS_TOKEN, Requester::ACCESS_TOKEN_CACHE_TTL);

        $mockResponse = $this->getMockAccessTokenResponse(array(
            'access_token' => self::MOCK_ACCESS_TOKEN
        ));

        $this->mockGuzzle->shouldReceive('post')
            ->with('/v1/oauth/token', m::any())
            ->once()
            ->andReturn($mockResponse);

        $token = $requester->getAccessToken();

        $this->assertEquals(self::MOCK_ACCESS_TOKEN, $token);
    }

    public function testCreateRequestParams()
    {
        $data = array(
            'test' => 'value'
        );

        $params = $this->requester->createRequestParams($data);

        $this->assertArrayHasKey('headers', $params);
        $this->assertEquals(self::MOCK_ACCESS_TOKEN, $params['headers']['x-access-token']);

        $this->assertArrayHasKey('test', $params);
        $this->assertEquals('value', $params['test']);
    }

    public function testCreateRequestParamsForEmptyArray()
    {
        $params = $this->requester->createRequestParams(array());

        $this->assertArrayHasKey('headers', $params);
        $this->assertEquals(self::MOCK_ACCESS_TOKEN, $params['headers']['x-access-token']);
    }

    public function testRunGetWithAccessToken()
    {
        $testArray = $this->setHttpMethodExpectations();

        $response = $this->requester->runGet('test-uri', $testArray);

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

    private function setCacheExpectations($hasCacheData, $cacheData = null, $dataToCache = null)
    {
        $this->mockCache->shouldReceive('has')
            ->once()->andReturn($hasCacheData);

        if($hasCacheData) {
            $this->mockCache->shouldReceive('get')
                ->once()->andReturn($cacheData);
        } else {
            $this->mockCache->shouldReceive('put')
                ->once()->with($dataToCache);
        }
    }

}
 