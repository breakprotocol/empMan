<?php
session_start();

//Include Google client library 
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = '236362381871-92ftd0rhn3i43sq962jjm0mcdk0uqql7.apps.googleusercontent.com';
$clientSecret = 'pE4Xf1tDnIrOpS0Fq4D9dDSe';
$redirectURL = 'http://localhost/boxxitAngularBackend/';

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login to CodexWorld.com');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>