<?php namespace Credibility\DandB;

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\ToArrayInterface;

class Response implements ToArrayInterface {

    /** @var array */
    protected $response;

    /** @var int */
    protected $status;

    /** var int */
    protected $errorCode;

    /** @var ResponseErrorCodes */
    protected $responseErrorCodes;

    /**
     * Creates a response object
     * @param ResponseInterface $responseInterface
     * @throws ParseException
     */
    public function __construct(ResponseInterface $responseInterface)
    {
        $this->response = $responseInterface->json();
        $this->status = isset($this->response['meta']['code']) ? $this->response['meta']['code'] : 500;
        $this->errorCode = isset($this->response['meta']['error_code']) ? $this->response['meta']['error_code'] : null;
        $this->responseErrorCodes = new ResponseErrorCodes();
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

    /**
     * Get the error code if it is set
     *
     * @returns int|null
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * Compare error code with passed in constant
     * @var string $code
     *
     * @returns bool
     */
    public function hasErrorCode($code) {
        return $this->responseErrorCodes->getConstant($code) === $this->errorCode;
    }

}
