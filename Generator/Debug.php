<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class AditionApiException extends Exception { }

class Adition
{
  // service for banners
  const SERVICE_BANNER = 'banner';
  // service for orders
  const SERVICE_ORDER = 'order';
  // service for reports
  const SERVICE_REPORT = 'report';
  // service for landingpages
  const SERVICE_LANDINGPAGE = 'landingpage';
  // service for metacollection
  const SERVICE_COLLECTION = 'soacollection';
  // service for website
  const SERVICE_WEBSITE = 'website';
  // service for company
  const SERVRICE_COMPANY = 'company';
  // service for campaign
  const SERVICE_CAMPAIGN = 'campaign';
  // service for contentunitgroup
  const SERVICE_CONTENTUNITGROUP = 'contentunitgroup';
  
  /**
   * @var string
   */
  const MODE_RPC = 'rpc';

  /**
   * @var string
   */
  const MODE_DOCUMENT = 'document';

  /**
   * @var string
   */
  protected $username = '';
  
  /**
   * @var string
   */ 
  protected $mode = self::MODE_RPC;

  /**
   * @var string
   */
  protected $password = '';

  /**
   * @var integer
   */
  protected $network = 0;

  /**
   * @var SoapClient
   */
  protected $active_service;
  
  /**
   * @var string
   */
  protected $service_name;

  /**
   * @var string
   */
  static $cookie = '';

  /**
   * @var array
   */
  protected $stats = array();

  /**
   * @var boolean
   */
  protected $debug = true;

  protected $functions = null;

  /**
   * 
   * @param string $username    Username used for authentification
   * @param string $password    Password used for authentification
   * @param integer $network_id Network ID used for authentification
   */
  public function __construct($username, $password, $network_id)
  {
    $this->setUsername($username);
    $this->setPassword($password);
    $this->setNetwork($network_id);
  }
  
  public function setMode($mode)
  {
    $this->mode = $mode;
  }

  /**
   * Username used for authentification
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }

  /**
   * Password used for authentification
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }

  /**
   * Network ID used for authentification
   * @param integer $network_id 
   */
  public function setNetwork($network_id)
  {
    $this->network = $network_id;
  }

  /**
   * Set the service for all following operations
   * @param string $service Service to call
   * @param array  $options Additional options passed to the SoapClient 
   */
  public function setService($service, array $options = array())
  {
    $this->functions = null;
    $this->service_name = $service;
    return $this->setClient($this->getWSDLForService($service), $options);
  }

  public function setClient($wsdl, array $options = array())
  {
    //! enable trace to be able to fetch the cookie out of the response header
    $default = array('trace' => true);

    //! instantiate SoapClient, merged passed $options with $default options
    $this->active_service = new SoapClient($wsdl, array_merge($options, $default));

    //! if there is a cookie, set it. otherwise request login
    if ($this->hasCookie() === true) {
      $this->setSoapCookie($this->getCookie());
    } else {
      $return = $this->login($this->username, $this->password, $this->network);

      //! exception: login failed.
      if ($return === false) {
        throw new AditionApiException('Login failed');
      }
    }
  }

  /** 
   * @param string $service Service name, e.g. "banner"
   */
  public function getWSDLForService($service)
  {
    if ($this->mode === self::MODE_RPC) {
      $file = '';
    } else {
      $file = '.document';
    }
    
    return 'http://soa.adition.com/soa/mw'.$service.$file.'.wsdl';
  }

  /**
   * Automagical calls the requested SoapFunction

   * @param string $function_name Service function to call 
   * @param array  $arguments     Arguments passed to the function

   * @throws AditionApiException

   * @return mixed
   */
  public function __call($function_name, $arguments)
  { 
    $time = microtime(true);

    //! always append the cookie if its there
    if ($this->hasCookie() === true) {
      $this->setSoapCookie($this->getCookie());
    }
    
    foreach($arguments as $key => $argument) {
      if (is_array($argument)) {
        $arguments[$key] = json_encode($argument);
      }
    }
    
    
    if ($this->mode === self::MODE_DOCUMENT) {
      $arguments_array = $arguments;
      $arguments = new stdClass();
      foreach($arguments_array as $id => $argument) {
        $calls = $this->getCallForFunction($function_name);
        $arguments->$calls[$id] = $argument;
      }
      
      $arguments = array($arguments);
    }
        
    try {
      $result = $this->getSoapClient()->__call($function_name, $arguments);
    } catch(SoapFault $e) {
      throw new AditionApiException($e->getMessage());
    }

    //! try to get the cookie from the response
    $this->saveCookieFromResponse();

    //! in debug mode, save time the time wasted for that call
    if ($this->debug === true) {
      if (isset($this->stats[$function_name]) === false) {
        $this->stats[$function_name] = 0;
      }
      $this->stats[$function_name] += (microtime(true)-$time);
    }
    
    if ($this->debug) {
      print $function_name.":\t".var_export($result, 1).PHP_EOL;
    }
    
    return $result;
  }
  
  protected function getCallForFunction($get_name)
  {
    if ($this->functions === null) {
      $mode = $this->mode;
      $this->setMode(self::MODE_RPC);      
      $a = new SoapClient($this->getWSDLForService($this->service_name));
      $this->setMode($mode);
      
      $this->functions = array();
      foreach($a->__getFunctions() as $function) {        
        list($returns, $function) = explode(' ', $function, 2);
        list($function, $calls) = preg_split("/[()]+/", $function);
        $calls = explode(', ', $calls);
        foreach($calls as $id => $call) {
          $name = str_replace('$', '', $call);
          @list($type, $name) = explode(' ', $name, 2);
          $calls[$id] = $name;
        }        
        
        $this->functions[$function] = $calls;
      }    
    }
    
    if (isset($this->functions[$get_name]) === false) {
      throw new AditionApiException('Function '.$get_name.' does not exist');
    }
    
    return $this->functions[$get_name];
  }

  /**
   * Returns active soap client

   * @throws AditionApiException If no service was set

   * @return SoapClient
   */
  public function getSoapClient()
  {
    if (($this->active_service instanceof SoapClient) === false) {
      throw new AditionApiException('No service set. Call '.__CLASS__.'::setService()');
    }

    return $this->active_service ;
  }

  /**
   * Sets the value of $cookie to the adition cookie
   * @param string $cookie
   * @return string
   */
  protected function setSoapCookie($cookie)
  {
    $cookie = $this->getCookie();
    $this->getSoapClient()->__setCookie('adition', $cookie);      
  }

  /**
   * @return null
   */
  protected function saveCookieFromResponse()
  { 
    //! pecl extension is missing, skip.
    if (function_exists('http_parse_headers') === false) {      
      return;
    }
    
    //! no set cookie command send by server, skip. 
    $header = http_parse_headers($this->getSoapClient()->__getLastResponseHeaders());
    if (!isset($header['Set-Cookie'])) {
      return;
    }

    //! no adition cookie found, skip.
    $header = http_parse_cookie($header['Set-Cookie']);
    if (!isset($header->cookies['adition'])) {
      return;   
    }

    //! got the cookie, save it for further usage.
    $this->setCookie($header->cookies['adition']);
  }

  /**
   * Returns current login token
   * @return string
   */
  public function getCookie()
  {
    return self::$cookie;
  }

  /**
   *
   * @param string $cookie
   */
  public function setCookie($cookie)
  {
    self::$cookie = $cookie;
  }


  /**
   * Returns if a cookie was set or detected
   * @return boolean
   */
  protected function hasCookie()
  {
    return self::$cookie !== '';
  }

  /**
   * Turns debug mode on (true) / off (false) 
   * @param boolean $toggle 
   */
  public function setDebug($toggle)
  {
    $this->debug = (bool) $toggle;
  }

  /**
   * Outputs some debug information if debug mode is enabled
   **/
  public function __destruct()
  {
    print "----------------------------\n";
    if ($this->debug === true) {
      foreach($this->stats as $call => $time) {
        print "$call:\t".round($time, 2)." ms\n";
      }
    }
  }
}

