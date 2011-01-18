<?php
namespace Wutil\Generator;
use SoapClient;

/**
 * This is the "Class Generator" to generate a class based on a WSDL.
 * You can use these classes to access a webservice.
 *
 * @author Moritz Schwoerer <moritz.schwoerer@gmail.com>
 * @since 2011/01/14
 */
class ClassGenerator
{
  /**
   * Location of WSDL file
   * @var string 
   */
  protected $wsdl;
 
  /**
   * 
   * @param string $wsdl
   */
  public function __construct($wsdl)
  {
    $this->wsdl = $wsdl;
  }

  /**
   *
   * @param string $target_file
   */
  public function generate($target_dir, $class_name, $namespace, $inherit, $template)
  {
    $client = $this->getClient();
    
    $vars = array(
        'methods' => array(),
        'class' => $class_name,
        'namespace' => $namespace,
        'inherit' => $inherit,
        'wsdl' => $this->wsdl
    );
        
    foreach($client->__getFunctions() as $function) {
      $vars['methods'][] = $this->parseFunction($function);      
    }
    
    file_put_contents($target_dir.'/'.$class_name.'.php', $this->fetchTemplate(__DIR__.'/../Templates/'.$template.'.php', $vars));
  }
  
  /**
   * Parses a string from the __getFunctions() array to an better accessible object 
   *
   * @param string $function the function string
   *   
   * @return Wutil\Generator\WSDLFunction
   */
  protected function parseFunction($function)
  {
    list($returns, $function) = explode(' ', $function, 2);
    list($function, $calls) = preg_split("/[()]+/", $function);
    
    $function = new WSDLFunction($function, trim($returns));
    
    $calls = explode(', ', $calls);
    foreach($calls as $call) {
      $name = explode(' ', str_replace('$', '', $call), 2);
      if (count($name) >= 2) {
        list($type, $name) = $name;
        $function->addParameter(new WSDLParameter($name, trim($type)));
      }
    }    

    return $function;
  }
  

  /**
   * Fetches and proccesses a template
   *
   * @param string $template_path Location of template
   * @param array  $allocations   An array of variables that will be available in the template
   *
   * @return string
   */
  private function fetchTemplate($template_path, array $allocations)
  {
    try {
      ob_start();
      extract($allocations);
      require $template_path;
      $ret = ob_get_contents();
      ob_end_clean();
      
    } catch(\Exception $e) {
      ob_get_contents();
      ob_end_clean();
      print $e->getMessage()."\n";
      print $e->getTraceAsString();
      throw $e;
    }
    
    return "<\x3fphp\n".$ret;
  }
  
  /**
   *
   *
   */
  protected function getClient()
  {
    return new SoapClient($this->getWSDL());
  }
  
  public function getWSDL()
  {
    return $this->wsdl;
  }
}
