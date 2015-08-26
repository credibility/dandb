Dun &amp; Bradstreet Emerging Business
=====
[![Build Status](https://travis-ci.org/credibility/dandb.svg)](https://travis-ci.org/credibility/dandb)
[![Coverage Status](https://img.shields.io/coveralls/credibility/dandb.svg)](https://coveralls.io/r/credibility/dandb)
[![Packagist](http://img.shields.io/packagist/v/credibility/dandb.svg)](https://packagist.org/packages/credibility/dandb)

PHP Wrapper for the Dun &amp; Bradstreet Emerging Business RESTful API

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
    
    // See Doc Comments for parameters
    $dandb = DandB::getInstance('https://api.dandb.com', 'client-id', 'client-secret', array());
    
    //From here, you can use the $dandb object to make requests. See PHP Doc comments in the class for info on how to use.
    
    $response = $dandb->businessSearchByDuns('123456789');
    
    if($response->isValid()) {
      print_r($response->getResponseData());  
    } else {
      print_r($response->getErrors());
    }
    
    ?>
  
Note: each request has an optional access token parameter. This allows you to cache an access token on your own and then pass it into each request. Otherwise the DandB object will make a request to get an access token before the actual call. 
