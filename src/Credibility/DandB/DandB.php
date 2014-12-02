<?php namespace Credibility\DandB;

use Credibility\DandB\Response;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Credibility\DandB\Cache\CacheableInterface;
use LogicException;

class DandB {

    /** @var Requester */
    protected $requester;

    public function __construct(Requester $requester)
    {
        $this->requester = $requester;
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
        $guzzleOpts = array(),
        CacheableInterface $cache = null,
        $accessToken = null
    )
    {
        $clientFactory = new ClientFactory($baseUrl, $guzzleOpts);
        $requester = new Requester($clientFactory, $clientId, $clientSecret, $cache, $accessToken);
        return new DandB($requester);
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
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function internationalSearchByDuns($duns)
    {
        return $this->requester->runGet('/v1/business/search/international', array(
            'duns' => $duns
        ));
    }

    /**
     * Given a Business Name and a country, run an international search
     *
     * Returns an array of results or false if an error occurred
     *
     * @param $name
     * @param $country
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function internationalSearchByNameCountry($name, $country)
    {
        return $this->requester->runGet('/v1/business/search/international', array(
            'name' => $name,
            'country' => $country,
        ));
    }

    /**
     * Searches businesses based on DUNS Number
     *
     * @param $duns
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByDuns($duns)
    {
        return $this->requester->runGet('/v1/business/search', array(
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
     * @return Response
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

        return $this->requester->runGet('/v1/business/search', $array);
    }

    /**
     * Searches businesses by phone number
     *
     * @param $phone
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function businessSearchByPhone($phone)
    {
        return $this->requester->runGet('/v1/business/search', array(
            'phone' => $phone
        ));
    }

    /**
     * Returns Verified information based on D&B Enterprise Business ID
     *
     * @param $businessId
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfile($businessId)
    {
        return $this->requester->runGet("/v1/verified/$businessId", array());
    }

    /**
     * Returns Verified information based on DUNS Number
     *
     * @param $duns
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function verifiedProfileWithDuns($duns)
    {
        return $this->requester->runGet("/v1/verified/$duns", array(
           'duns' => true
        ));
    }

    /**
     * Returns a User Token for an existing user
     *
     * @param $email
     * @param $password
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userToken($email, $password)
    {
        return $this->requester->runPost('/v1/user/token', array(
            'email' => $email,
            'password' => $password
        ));
    }

    /**
     * Logout a user from OWL
     * @return Response
     */
    public function userLogout($userToken)
    {
        return $this->requester->runPost('/v1/user/logout', array(
            'user_token' => $userToken
        ));
    }

    /**
     * Returns User based on User Token
     *
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userUsingToken($userToken, $accessToken = null)
    {
        return $this->requester->runGet('/v1/user/token/status', array(
            'user_token' => $userToken
        ));
    }

    /**
     * Send out an email from owl to reset the password
     * @param $email
     * @return Response
     */
    public function passwordReset($email)
    {
        return $this->requester->runPost('/v1/user/password/reset', array(
            'email' => $email
        ));
    }

    /**
     * Allow a user to change their password
     * @param  $userToken
     * @param  $oldPassword
     * @param  $newPassword
     * @return Response
     */
    public function passwordChange($userToken, $oldPassword, $newPassword)
    {
        return $this->requester->runPost('/v1/user/password/change', array(
            'user_token' => $userToken,
            'old_password' => $oldPassword,
            'new_password' => $newPassword
        ));
    }

    /**
     * Allow a user to register a new account
     * @param  $email
     * @param  $firstName
     * @param  $lastName
     * @param  $phone
     * @param  $password
     * @param  $acceptedTOS must be 1
     * @return Response
     */
    public function userRegister($email, $first_name, $last_name, $accepted_tos, $password=null, $phone_number=null, $address_line_1=null, $address_line_2=null, $address_line_3=null, $city=null, $state_code=null, $postal_code=null, $source=null)
    {
        //required
        $params = compact('email','first_name','last_name','accepted_tos');
        //optional (only add to params if not empty)
        $optionalParams = array('password','phone_number','address_line_1','address_line_2','address_line_3','city','state_code','postal_code','source');
        foreach($optionalParams as $optional) {
            if(!empty($$optional)) {
                $params[$optional] = $$optional;
            }
        }

        return $this->requester->runPost('/v1.1/user/register', $params);
    }

    /**
     * Returns User Entitlements based on User Token
     *
     * @see DandB::userToken
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userEntitlements($userToken)
    {
        return $this->requester->runGet('/v1.1/user/entitlements', array(
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
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userTokenRefresh($email, $refreshToken)
    {
        return $this->requester->runPost('/v1/user/token/refresh', array(
            'email' => $email,
            'refresh_token' => $refreshToken
        ));
    }

    /**
     * Return the status of a user based on a user token
     *
     * @see DandB::userToken
     * @param $userToken
     * @return Response
     * @throws RequestException|LogicException|ParseException
     */
    public function userTokenStatus($userToken)
    {
        return $this->requester->runGet('/v1/user/token', array(
            'user_token' => $userToken
        ));
    }

}
