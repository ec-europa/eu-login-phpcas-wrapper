<?php

namespace webtools\app\ecas;


class TestClient{
  private static $phpcasClientCalled = FALSE;
  protected $ecasConfig;

  public function __construct($_ecas_config) {
    $this->ecasConfig = $_ecas_config;

    //require_once $this->ecasConfig['library'];

    if (self::$phpcasClientCalled === FALSE) {
      // Instantiate or re-instantiate CAS object.
      \phpCAS::client(
        $this->ecasConfig['version'],
        $this->ecasConfig['host'],
        (int) $this->ecasConfig['port'],
        $this->ecasConfig['uri'],
        FALSE
      );

      self::$phpcasClientCalled = TRUE;
    }

    // List of allowed proxies for ticket validation.
    \phpCAS::allowProxyChain(
      new \CAS_ProxyChain($this->ecasConfig['allowed_proxy_chain'])
    );

    if ($this->ecasConfig['cert']) {
      \phpCAS::setCasServerCACert($this->ecasConfig['cert']);
    }
    else {
      \phpCAS::setNoCasServerValidation();
    }

    $this->setValidatorExtraFields();
  }


  public function login($force_auth = TRUE) {

    if ($force_auth) {
      //set the strengths limitation first if any are provided in configuration
      $this->setStrengths();

      \phpCAS::forceAuthentication();
    }

    if (!\phpCAS::checkAuthentication()) {
      return array(
        'success' => FALSE,
        'message' => "No Authentication",
        'user'    => NULL,
      );
    }
    $username = \phpCAS::getUser();
    $userData  = \phpCAS::getAttributes();

    return array(
      'success' => TRUE,
      'message' => "Login Successfully",
      'username'    => $username,
      'userdata'    => $userData,
    );
  }

  /**
   * @desc Check whether there are some strengths set in the configuration and if true generate the LoginURL with the limitaion of accepted strengths.
   */
  private function setStrengths(){

    if(trim($this->ecasConfig['userStrengths'])){
      $acceptedStrengths = explode(',', $this->ecasConfig['userStrengths']);
      $strengths = '';

      foreach ($acceptedStrengths as $strength) {
        $strengths = (trim($strengths) === "" ? "" : ",") . urlencode($strength);
      }

      \phpCAS::setServerLoginURL(\phpCAS::getServerLoginURL() . "&acceptStrengths=" . $strengths);
    }
  }


  public function logout(){

    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    if (isset($_SESSION['phpCAS'])) {
      unset($_SESSION['phpCAS']);
    }

    \phpCAS::logout();
  }

  /**
   * @desc The method will set the ServerProxyValidateURL or ServerServiceValidateURL depending whether allowed proxy chain is present in configuration
   *       The values which will be sent are:
   *          - userDetails in order to get all user details
   *          - groups in order to map all intended groups
   *          - assuranceLevel in order to filter the desired assuranceLevel.
   */
  private function setValidatorExtraFields(){
    
    if(is_array($this->ecasConfig['allowed_proxy_chain']) && sizeof($this->ecasConfig['allowed_proxy_chain']) > 0){
      $validateUri = $this->ecasConfig['proxy_validate_uri'] ;
    }else{
      $validateUri = $this->ecasConfig['service_validate_uri'] ;
    }
    
    //added in order to display user details and enforce assurance_level
    $validateServerUrl = 'https://' . $this->ecasConfig['host'] . ":" ; 
    $validateServerUrl .= $this->ecasConfig['port'] ;
    $validateServerUrl .= $this->ecasConfig['uri'] ;
    $validateServerUrl .= $validateUri . '?' ;
    $validateServerUrl .= 'userDetails=' . $this->ecasConfig['user_details'] ;
    $validateServerUrl .= '&groups=' . $this->ecasConfig['user_groups'] ;
    $validateServerUrl .= '&assuranceLevel=' . $this->ecasConfig['user_assurance_level'] ;

    if(is_array($this->ecasConfig['allowed_proxy_chain']) && count($this->ecasConfig['allowed_proxy_chain']) > 0){
      \phpCAS::setServerProxyValidateURL($validateServerUrl);
    }else{
      \phpCAS::setServerServiceValidateURL($validateServerUrl);
    }
  }
}