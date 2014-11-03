<?php namespace Credibility\DandB;

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use LogicException;

class DandB {

    /** @var Requester */
    protected $requester;

    public function __construct(Requester $requester)
    {
        $this->requester = $requester;
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
        return $this->requester->getAccessToken();
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
    public function internationalSearchByDuns($duns, $accessToken = null)
    {
        return $this->requester->runGet('/v1/business/search/international', array(
            'duns' => $duns,
        ), $accessToken);
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
    public function internationalSearchByNameCountry($name, $country, $accessToken = null)
    {
        return $this->requester->runGet('/v1/business/search/international', array(
            'name' => $name,
            'country' => $country,
        ), $accessToken);
    }

    /**
     * Searches businesses based on DUNS Number
     *
     * @param $duns
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByDuns($duns, $accessToken = null)
    {
        return $this->requester->runGet('/v1/business/search', array(
            'duns' => $duns,
        ), $accessToken);

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
    public function businessSearchByNameAddress($name, $state, $address = null, $city = null, $zip = null, $accessToken = null)
    {
        $array = array(
            'name' => $name,
            'state' => $state,
        );

        if(!is_null($address)) { $array['address'] = $address; }
        if(!is_null($city)) { $array['city'] = $city; }
        if(!is_null($zip)) { $array['zip'] = $zip; }

        return $this->requester->runGet('/v1/business/search', $array, $accessToken);
    }

    /**
     * Searches businesses by phone number
     *
     * @param $phone
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByPhone($phone, $accessToken = null)
    {
        return $this->requester->runGet('/v1/business/search', array(
            'phone' => $phone
        ), $accessToken);
    }

    /**
     * Returns Verified information based on D&B Enterprise Business ID
     *
     * @param $businessId
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfile($businessId, $accessToken = null)
    {
        return $this->requester->runGet("/v1/verified/$businessId", array(), $accessToken);
    }

    /**
     * Returns Verified information based on DUNS Number
     *
     * @param $duns
     * @param null $accessToken
     * @return \Credibility\DandB\Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfileWithDuns($duns, $accessToken = null)
    {
        return $this->requester->runGet("/v1/verified/$duns", array(
           'duns' => true
        ), $accessToken);
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
    public function userToken($email, $password, $accessToken = null)
    {
        return $this->requester->runPost('/v1/user/token', array(
            'email' => $email,
            'password' => $password
        ), $accessToken);
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
    public function userEntitlements($userToken, $accessToken = null)
    {
        return $this->requester->runGet('/v1.1/user/entitlements', array(
            'user_token' => $userToken
        ), $accessToken);
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
    public function userTokenRefresh($email, $refreshToken, $accessToken = null)
    {
        return $this->requester->runPost('/v1/user/token/refresh', array(
            'email' => $email,
            'refresh_token' => $refreshToken
        ), $accessToken);
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
    public function userTokenStatus($userToken, $accessToken = null)
    {
        return $this->requester->runGet('/v1/user/token', array(
            'user_token' => $userToken
        ), $accessToken);
    }


}
