<?php namespace Credibility\DandB;

use GuzzleHttp\Client;

class ClientFactory {

    /** @var string */
    protected $baseUrl;

    /** @var array */
    protected $guzzleOpts;

    public function __construct($baseUrl, $guzzleOpts = array())
    {
        $this->baseUrl = $baseUrl;
        $this->guzzleOpts = $guzzleOpts;
    }

    public function createClient()
    {
        $config = array(
            'base_url' => $this->baseUrl,
        );

        $config = array_merge($config, $this->guzzleOpts);

        return new Client($config);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getGuzzleOpts()
    {
        return $this->guzzleOpts;
    }

}
