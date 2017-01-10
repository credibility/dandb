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

    public function __construct(
        ClientFactory $clientFactory,
        $clientId, $clientSecret,
        CacheableInterface $cache = null,
        $accessToken = null
    )
    {
        $this->guzzleClient = $clientFactory->createClient();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cache = $cache;
        $this->accessToken = $accessToken;
    }

    public function runGet($uri, $data = array())
    {
        $requestData = array(
            'query' => $data
        );
        return $this->formatRequest('GET', $uri, $requestData);
    }

    public function runPost($uri, $data = array())
    {
        $requestData = array(
            'body' => $data
        );
        return $this->formatRequest('POST', $uri, $requestData);
    }

    public function runJsonPost($uri, $data = array())
    {
        $requestData = array(
            'body' => json_encode($data)
        );
        return $this->formatJsonRequest('POST', $uri, $requestData);
    }

    public function formatJsonRequest($method, $uri, $data = array())
    {
        if(is_null($this->accessToken)) {
            $this->accessToken = $this->getAccessToken();
        }
        $requestParams = $this->createJsonRequestParams($data);

        return $this->executeJson($method, $uri, $requestParams);
    }

    public function formatRequest($method, $uri, $data = array())
    {
        if(is_null($this->accessToken)) {
            $this->accessToken = $this->getAccessToken();
        }
        $requestParams = $this->createRequestParams($data);

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

    public function executeJson($method, $uri, $data)
    {
        $request = $this->guzzleClient->createRequest($method, $uri, $data);
        /** @var ResponseInterface $response */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = $this->guzzleClient->send($request);
        return $response->json();
    }

    public function createRequestParams($data)
    {
        $header = array('headers' => array());
        $header['headers']['x-access-token'] = $this->accessToken;
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

    public function createJsonRequestParams($data)
    {
        $header = array('headers' => array());
        $header['headers']['x-access-token'] = $this->accessToken;
        $header['headers']['Content-Type'] = 'application/json';
        if(isset($data['body'])) {
            $requestBodyArray = json_decode($data['body'], true);
            if(isset($requestBodyArray['user_token'])) {
                $header['headers']['user-token'] = $requestBodyArray['user_token'];
                unset($requestBodyArray['user_token']);
                $data['body'] = json_encode($requestBodyArray);
            }
        }

        $requestParams = array_merge(
            $header,
            $data
        );
        echo "<pre>";
        print_r($requestParams);
        return $requestParams;

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
                return $token;
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