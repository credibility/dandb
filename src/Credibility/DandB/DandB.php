<?php namespace Credibility\DandB;

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Credibility\DandB\Cache\CacheableInterface;
use LogicException;

class DandB {

    /** @var Requester */
    protected $requester;
    private $cache;
    private $clientId;
    private $clientSecret;

    public function __construct($clientId, $clientSecret, Requester $requester, CacheableInterface $cacheableInterface = null, array $options = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->requester = $requester;
        $this->cache = $cacheableInterface;
        $options['access-token-key'] = empty($options['access-token-key']) ? 'dbcc-access-token' : $options['access-token-key'];
        $this->options = $options;
    }

    /**
     * Factory method to create DandB object
     * @param string $baseUrl
     * @param $clientId
     * @param $clientSecret
     * @param array $guzzleOpts
     * @return DandB
     */
    public static function getInstance(
        $clientId, $clientSecret,
        $baseUrl = 'https://api.dandb.com',
        $guzzleOpts = array()
    )
    {
        $clientFactory = new ClientFactory($baseUrl, $guzzleOpts);
        $requester = new Requester($clientFactory);
        return new DandB($clientId, $clientSecret, $requester);
    }

    /**
     * Returns an access token if available, otherwise false.
     * Note: tokens last for 1 hour. It's up to the client to
     * cache this token if needed
     *
     * @return bool|string
     * @throws RequestException|LogicException|ParseException
     */
    public function getAccessToken()
    {
        $cacheKey = $this->options['access-token-key'];
        if($this->cache) {

            if ($this->cache->has($cacheKey)) {
                return $this->cache->get($cacheKey);
            }
        }

        /** @var ResponseInterface $response */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = $this->requester->runPost('/v1/oauth/token', false, array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials'
            )
        );

        if(isset($response['access_token'])) {
            $key = $response['access_token'];
            if($this->cache) {
                $this->cache->set($cacheKey, $key);
            }
            return $key;
        }
        return false;
    }

    /**
     * Given a DUNS, run an international search
     *
     * Returns an array of results or false if an error occurred
     *
     * @param $duns
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function internationalSearchByDuns($duns)
    {
        return $this->requester->runGet('/v1/business/search/international', $this->getAccessToken(), array(
            'duns' => $duns,
        ));
    }

    /**
     * Given a Business Name and a country, run an international search
     *
     * Returns an array of results or false if an error occurred
     *
     * @param $name
     * @param $country
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function internationalSearchByNameCountry($name, $country)
    {
        return $this->requester->runGet('/v1/business/search/international', $this->getAccessToken(), array(
            'name' => $name,
            'country' => $country,
        ));
    }

    /**
     * Searches businesses based on DUNS Number
     *
     * @param $duns
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByDuns($duns)
    {
        return $this->requester->runGet('/v1/business/search', $this->getAccessToken(), array(
            'duns' => $duns,
        ));
    }

    /**
     * Searches businesses by name and address
     *
     * @param $name
     * @param $state
     * @param null $address
     * @param null $city
     * @param null $zip
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByNameAddress($name, $state, $address = null, $city = null, $zip = null)
    {
        $array = array(
            'name' => $name,
            'state' => $state,
        );

        if(!is_null($address)) { $array['address'] = $address; }
        if(!is_null($city)) { $array['city'] = $city; }
        if(!is_null($zip)) { $array['zip'] = $zip; }

        return $this->requester->runGet('/v1/business/search', $this->getAccessToken(), $array);
    }

    /**
     * Searches businesses by phone number
     *
     * @param $phone
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByPhone($phone)
    {
        return $this->requester->runGet('/v1/business/search', $this->getAccessToken(), array(
            'phone' => $phone
        ));
    }

    /**
     * Returns Verified information based on D&B Enterprise Business ID
     *
     * @param $businessId
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfile($businessId)
    {
        return $this->requester->runGet("/v1/verified/$businessId", $this->getAccessToken());
    }

    /**
     * Returns Verified information based on DUNS Number
     *
     * @param $duns
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfileWithDuns($duns)
    {
        return $this->requester->runGet("/v1/verified/$duns", $this->getAccessToken(), array(
           'duns' => true
        ));
    }

    /**
     * Returns a User Token for an existing user
     *
     * @param $email
     * @param $password
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userToken($email, $password)
    {
        return $this->requester->runPost('/v1/user/token', $this->getAccessToken(), array(
            'email' => $email,
            'password' => $password
        ));
    }

    /**
     * Returns User Entitlements based on User Token
     *
     * @see DandB::userToken
     * @param $userToken
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userEntitlements($userToken)
    {
        return $this->requester->runGet('/v1.1/user/entitlements', $this->getAccessToken(), array(
            'user_token' => $userToken
        ));
    }

    /**
     * Returns new user token based on refresh token from
     * the User Token call
     *
     * @see DandB::userToken
     * @param $email
     * @param $refreshToken
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userTokenRefresh($email, $refreshToken)
    {
        return $this->requester->runPost('/v1/user/token/refresh', $this->getAccessToken(), array(
            'email' => $email,
            'refresh_token' => $refreshToken
        ));
    }

    /**
     * Return the status of a user based on a user token
     *
     * @see DandB::userToken
     * @param $userToken
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userTokenStatus($userToken)
    {
        return $this->requester->runGet('/v1/user/token', $this->getAccessToken(), array(
            'user_token' => $userToken
        ));
    }

}
