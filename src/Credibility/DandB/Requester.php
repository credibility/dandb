<?php namespace Credibility\DandB;

use Credibility\DandB\Cache\CacheableInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use LogicException;

class Requester {

    const ACCESS_TOKEN_CACHE_KEY = 'access-token-cache-key';

    const ACCESS_TOKEN_CACHE_TTL = 36000;

    protected $clientId;

    protected $clientSecret;

    /** @var Client */
    protected $guzzleClient;

    /** @var CacheableInterface */
    protected $cache;

    /** @var string */
    protected $accessToken;

    public function __construct(ClientFactory $clientFactory, $clientId, $clientSecret, CacheableInterface $cache = null, $accessToken = null)
    {
        $this->guzzleClient = $clientFactory->createClient();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cache = $cache;
        $this->accessToken = $accessToken;
    }

    public function runGet($uri, $accessToken, $data = array())
    {
        $requestData = array(
            'query' => $data
        );
        return $this->formatRequest('GET', $uri, $accessToken, $requestData);
    }

    public function runPost($uri, $accessToken = false, $data = array())
    {
        $requestData = array(
            'body' => $data
        );
        return $this->formatRequest('POST', $uri, $accessToken, $requestData);
    }

    public function formatRequest($method, $uri, $accessToken, $data = array())
    {
        $requestParams = $this->createRequestParams($data, $accessToken);

        return $this->execute($method, $uri, $requestParams);
    }

    public function execute($method, $uri, $data)
    {
        $request = $this->guzzleClient->createRequest($method, $uri, $data);
        /** @var ResponseInterface $response */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = $this->guzzleClient->send($request);
        return new Response($response);
    }

    public function createRequestParams($data)
    {
        $header = array('headers' => array());
        if($accessToken = $this->getAccessToken()) {
            $header['headers']['x-access-token'] = $accessToken;
        }
        if(count($data) == 0) {
            return $header;
        } else {
            $requestParams = array_merge(
                $header,
                $data
            );
            return $requestParams;
        }
    }

    public function getAccessToken()
    {
        if($this->accessToken) {
            return $this->accessToken;
        }

        if($this->cacheExists()) {
            if ($this->cache->has(self::ACCESS_TOKEN_CACHE_KEY)) {
                return $this->cache->get(self::ACCESS_TOKEN_CACHE_KEY);
            } else {
                $token = $this->postAccessToken();
                $this->cache->put(self::ACCESS_TOKEN_CACHE_KEY, $token, self::ACCESS_TOKEN_CACHE_TTL);
            }
        }

        return $this->postAccessToken();
    }

    public function postAccessToken()
    {
        /** @var ResponseInterface $response */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = $this->guzzleClient->post('/v1/oauth/token', array(
            'body' => array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials'
            )
        ));

        $response = $response->json();
        if (isset($response['access_token'])) {
            return $response['access_token'];
        }
        return false;
    }

    public function cacheExists()
    {
        return !is_null($this->cache);
    }
} 