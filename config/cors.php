<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
   
	'supportsCredentials' => false,
	'allowedOrigins' => ['*'],
	'allowedHeaders' => ['accept, content-type,x-xsrf-token, x-csrf-token'],
	'allowedMethods' => ['POST, GET, OPTIONS, PUT, DELETE'],
	'exposedHeaders' => [],
	'maxAge' => 0,
	'hosts' => [],

];
