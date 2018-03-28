<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/testClient.php';

use webtools\app\ecas;

$config = array(
  'version' => '2.0',
  'host' => 'webgate.ec.europa.eu',
  'port' => '443',
  'uri' => '/cas',
  'proxy_validate_uri' => '/proxyValidate', //do not change, it is used when proxy chain is specified
  'service_validate_uri' => '/laxValidate', //do not change, it is used when no proxy chain is specified
  'user_details' => 'true', //this will return all user fields kept in ECAS server and available to be sent
  'user_groups' => '*', //* means all user groups
  'user_assurance_level' => 'TOP', //types of assurance level TOP/HIGH/MEDIUM/LOW
  'userStrengths' => 'STRONG', //add strenghts separated by comma
  'debug' => FALSE,
  'cert' => 'PATH_TO_CERTIFICATE',
  'logout' => 'app',
  'allowed_proxy_chain' => array(
    '/^https?:\/\/[^\/]*\.europa\.eu\//',
  ),
);


//USAGE :
//localhost/test.php to login
//localhost/test.php?action=logout to logout
if(isset($_GET['action']) && $_GET['action'] == 'logout') {

##################login
  $client   = new ecas\TestClient($config);
  $client->logout();
}else{
  ##################login
  $client   = new ecas\TestClient($config);
  $response = $client->login();

  //display user details
  var_dump($response);
}


