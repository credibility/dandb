Dun &amp; Bradstreet Credibility Corp. [UNOFFICIAL]
=====
[![Build Status](https://travis-ci.org/credibility/dandb.svg)](https://travis-ci.org/credibility/dandb)
[![Coverage Status](https://img.shields.io/coveralls/credibility/dandb.svg)](https://coveralls.io/r/credibility/dandb)

PHP Wrapper for the Dun &amp; Bradstreet Credibility Corp. RESTful API

To use with Laravel, see [credibility/dandb-laravel](https://github.com/credibility/dandb-laravel)

## Installation

Install using composer:

    "require": {
      "credibility/dandb": "dev-master"  
    }

## Usage

See below for sample initialization code:

    <?php
    
    include_once 'vendor/autoload.php';
    
    /** 
     * The first parameter is the Host, the second is an array of Guzzle options
     * for the guzzle client directly
     */
    $clientFactory = new Credibility\DandB\ClientFactory('https://api.dandb.com', array());
    
    $requester = new Credibility\DandB\Requester($clientFactory, 'client-id', 'client-secret');
    
    $dandb = new Credibility\DandB\DandB($requester);
    
    //From here, you can use the $dandb object to make requests. See PHP Doc comments in the class for info on how to use.
    
    $response = $dandb->businessSearchByDuns('123456789');
    
    if($response->isValid()) {
      print_r($response->getResponseData());  
    } else {
      print_r($response->getErrors());
    }
    
    ?>
  
Note: each request has an optional access token parameter. This allows you to cache an access token on your own and then pass it into each request. Otherwise the DandB object will make a request to get an access token before the actual call. 
