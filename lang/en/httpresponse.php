<?php

return [

    /**
     * --------------------------------------------------------------------------
     * HttpResponse Language Lines
     * --------------------------------------------------------------------------
     * 
     */

    '100' => 'Continue',
    '101' => 'Switching Protocols',

    '200' => 'Success',
    '201' => 'Created',
    '202' => 'Accepted',
    '204' => 'No Content',

    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '409' => 'Conflict',
    '422' => 'Unprocessable Entity',

    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',

    'default' => 'Unknown Error',
    
    '100.description' => 'The server has received the request headers, and the client should proceed to send the request body.',
    '101.description' => 'The server is switching protocols, and the client should proceed to send the request using the new protocol.',

    '200.description' => 'The request has succeeded.',
    '201.description' => 'The request has been fulfilled and resulted in a new resource being created.',
    '202.description' => 'The request has been accepted for processing, but the processing has not been completed.',
    '204.description' => 'The server successfully processed the request, but is not returning any content.',

    '400.description' => 'The request could not be understood by the server due to malformed syntax.',
    '401.description' => 'The request requires user authentication.',
    '403.description' => 'The server understood the request, but is refusing to fulfill it.',
    '404.description' => 'The server has not found anything matching the request URI.',
    '405.description' => 'The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.',
    '409.description' => 'The request could not be completed due to a conflict with the current state of the resource.',
    '422.description' => 'The request was well-formed but was unable to be followed due to semantic errors.',

    '500.description' => 'The server encountered an unexpected condition which prevented it from fulfilling the request.',
    '501.description' => 'The server does not support the functionality required to fulfill the request.',
    '502.description' => 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.',
    '503.description' => 'The server is currently unable to handle the request due to a temporary overloading or maintenance of the server.',

    'default.description' => 'An unexpected condition was encountered.',
];