<?php

namespace Credibility\DandB;

class ResponseErrorCodes {

    // AUTH ERRORS
    CONST TRANSACTION_ID_IS_INVALID            = "ATH001";
    CONST BUSINESS_ID_IS_INVALID               = "ATH002";
    CONST QUESTION_SET_ALREADY_ATTEMPTED       = "ATH003";
    CONST QUESTION_SET_NOT_PRESENTED           = "ATH004";
    CONST INCORRECT_NUMBER_OF_QUESTIONS        = "ATH005";
    CONST INVALID_FILE_EXTENSION               = "ATH006";
    CONST ASSET_DOES_NOT_EXIST                 = "ATH007";
    CONST PHONE_VERIFICATION_ALREADY_ATTEMPTED = "ATH008";
    CONST QUESTION_SET_MAPPER_FIND_FAILURE     = "ATH009";
    CONST OAUTH_SERVER_EXCEPTION               = "ATH010";

    // USER ERRORS
    CONST USER_IS_NOT_ACTIVE                                = "USR001";
    CONST USER_ROUTE_INCORRECT_CREDENTIALS                  = "USR002";
    CONST USER_ROUTE_ACCOUNT_EMAIL_UNAVAILABLE              = "USR003";
    CONST USER_ROUTE_EMAIL_DOES_NOT_EXIST                   = "USR004";
    CONST USER_ROUTE_INCORRECT_OLD_PASSWORD                 = "USR005";
    CONST USER_ROUTE_PAYMENT_TYPE_INCORRECT                 = "USR006";
    CONST USER_ROUTE_ORDER_PREPARATION_FAILURE              = "USR007";
    CONST USER_ROUTE_ORDER_FAILURE                          = "USR008";
    CONST USER_ROUTE_PRODUCT_SHOULD_BE_FREE                 = "USR009";
    CONST USER_ROUTE_ALERT_PREFERENCE_UNAVAILABLE           = "USR010";
    CONST USER_ROUTE_INVALID_USER_TOKEN                     = "USR011";
    CONST USER_REGISTER_ROUTE_EMPTY_ADDRESS_PARAM           = "USR012";
    CONST USER_MAPPER_PREFERENCES_NOT_FOUND                 = "USR013";
    CONST USER_DISABLE_ERROR                                = "USR014";
    CONST USER_ENABLE_ERROR                                 = "USR015";
    CONST USER_ENABLE_ERROR_DISABLED_PARTNER_USER_NOT_FOUND = "USR016";
    CONST USER_MAPPER_ENABLE_USER_PASSWORD_ERROR            = "USR017";
    CONST USER_CONTROLLER_DISABLE_NOT_PARTNER_USER_ERROR    = "USR018";
    CONST USER_ROUTE_SIMILAR_PRODUCT_ALREADY_PURCHASED      = "USR019";
    CONST USER_TOKEN_INVALID                                = "USR020";
    CONST USER_TOKEN_MISSING                                = "USR021";
    CONST USER_DELETED                                      = "USR022";
    CONST USER_TOKEN_INVALID_EXPIRED                        = "USR023";
    CONST USER_LOGIN_ERROR                                  = "USR024";
    CONST TOS_ACCEPT_ERROR                                  = "USR025";
    CONST ERROR_DUPLICATE_ITEMS_IN_ORDER                    = "USR026";
    CONST INCORRECT_ORDER_SYNTAX                            = "USR027";
    CONST MALFORMED_PARAMETER                               = "USR028";
    CONST ADDRESS_FIELDS_MISSING                            = "USR029";
    CONST USER_TOKEN_EXPIRED                                = "USR030";
    CONST USER_MAPPER_ERROR_CANCELLATION_SERVICE            = "USR031";
    CONST USER_NOT_FOUND                                    = "USR032";
    CONST INCORRECT_USER_TYPE                               = "USR033";


    public function getConstant($code) {
        return constant('self::' . $code);
    }

}