<?php
namespace Wutil\Generator;

/**
 * This class describes a function defined in a WSDL file
 *
 * @author Moritz Schwoerer <moritz.schwoerer@gmail.com>
 * @since 2011/01/14
 */
class WSDLFunction
{
  /**
   * name of function
   * @var string
   */
  protected $name;
  
  /**
   * return type of function
   * @var string
   */
  protected $returns;
  
  /**
   * list of function parameters
   * @var array
   */
  protected $parameters = array();
  
  /**
   *
   * @param string $name
   * @param string $returns 
   */
  public function __construct($name, $returns)
  {
    $this->name = $name;
    $this->returns = $returns;
  }
  
  /**
   * Adds a parameter to the function
   * @param Wutil\Generator\WSDLParameter
   */
  public function addParameter(WSDLParameter $parameter) 
  {
    $this->parameters[] = $parameter;
  }
  
  /**
   * Gets the name of the function
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  
  /**
   * Gets the return type of the function
   *
   * @return string
   */
  public function getReturn()
  {
    return $this->returns;
  }
   
  /**
   * Gets the parameters of the function
   *
   * @return array
   */   
  public function getParameters()
  {
    return $this->parameters;
  }

  public function getInvokeArgs()
  {
    $args = array();
    foreach($this->getParameters() as $parameter) {
      $args[] = '$'.$parameter->getName();
    }

    return $args;
  }

}
