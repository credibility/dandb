<?php namespace Credibility\DandB;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use LogicException;

class Requester {

    protected $clientId;

    protected $clientSecret;

    /** @var Client */
    protected $guzzleClient;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->guzzleClient = $clientFactory->createClient();
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

    public function createRequestParams($data, $accessToken)
    {
        $header = array('headers' => array());
        if($accessToken) {
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
        if(isset($response['access_token'])) {
            return $response['access_token'];
        }
        return false;
    }

} 