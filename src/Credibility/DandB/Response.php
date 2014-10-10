<?php namespace Credibility\DandB;

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\ToArrayInterface;

class Response implements ToArrayInterface {

    /** @var array */
    protected $response;

    /** @var int */
    protected $status;

    /**
     * Creates a response object
     * @param ResponseInterface $responseInterface
     * @throws ParseException
     */
    public function __construct(ResponseInterface $responseInterface)
    {
        $this->response = $responseInterface->json();
        $this->status = isset($this->response['meta']['code']) ? $this->response['meta']['code'] : 500;
    }

    /**
     * Returns the response data in array if the request
     * was successful, false otherwise.
     * @return array|bool
     */
    public function getResponseData()
    {
        if(isset($this->response['response']['results'])) {
            return $this->response['response']['results'];
        }
        return false;
    }

    /**
     * Returns errors as an array if they exist,
     * false otherwise
     * @return array|bool
     */
    public function getErrors()
    {
        if(isset($this->response['error'])) {
            return $this->response['error'];
        }
        return false;
    }

    /**
     * Determines if the response was successful
     * @return bool
     */
    public function isValid()
    {
        return $this->status == 200;
    }

    /**
     * Returns status code
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * Get the array representation of an object
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
