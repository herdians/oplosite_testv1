<?php
$ip = '127.0.0.1'; //ip address alfresco di localhost
$port = '8080';

return [
    /*
    |--------------------------------------------------------------------------
    | CMIS BROWSER URL
    |--------------------------------------------------------------------------
    |
    | The address of Alfresco server
    |
    */

    'CMIS_BROWSER_URL' => "http://" . env('CMIS_HOST', $ip) . ":"  . env('CMIS_PORT', $port) . "/alfresco/api/-default-/public/cmis/versions/1.1/browser", //API untuk akses alfresco

	/*
    |--------------------------------------------------------------------------
    | CMIS BROWSER USER
    |--------------------------------------------------------------------------
    |
    | Alfresco username credential
    |
    */

	'CMIS_BROWSER_USER' => env('CMIS_USERNAME', 'admin'), //user dan password untuk masuk ke alfresco

	/*
    |--------------------------------------------------------------------------
    | CMIS BROWSER PASSWORD
    |--------------------------------------------------------------------------
    |
    | Alfresco password credential
    |
    */

	'CMIS_BROWSER_PASSWORD' => env('CMIS_PASSWORD', 'admin123'),

	/*
    |--------------------------------------------------------------------------
    | CMIS BROWSER URL
    |--------------------------------------------------------------------------
    |
    | If there are more than one repository in  Alfresco, mention it here. If
    | it is set to null, the first repository will be used
    |
    */

	'CMIS_REPOSITORY_ID' => null,

    'ALFRESCO_API' => "http://" . env('CMIS_HOST', $ip) . ":"  . env('CMIS_PORT', $port) . "/alfresco/service/api/", //API untuk akses alfresco

    // STATUSES
    'STATE_ON_PROGRESS' => 'on progress',
    'STATE_COMPLETED' => 'completed',
    'STATE_EXPIRED' => 'expired',
    'STATE_CANCELED' => 'canceled',

    'STATUS_PENDING' => 'pending',
    'STATUS_APPROVED' => 'approved',
    'STATUS_SIGNED' => 'signed',
    'STATUS_REJECTED' => 'rejected',
];
