<?php
namespace Wutil\Generator;

/**
 * This class describes a parameter defined in a WSDL file.
 * Its used by the WSDLFunction to describe its parameters
 *
 * @author Moritz Schwoerer <moritz.schwoerer@gmail.com>
 * @since 2011/01/14
 */
class WSDLParameter
{
  /**
   * Type of the parameter
   * @var string
   */
  protected $type;
  
  /**
   * Name of the parameter
   * @var string
   */
  protected $name;
  
  /**
   * @param string $name
   * @param string $type 
   */  
  public function __construct($name, $type)
  {
    $this->name = $name;
    $this->type = $type;  
  }
  
  public function getType()
  {
    return $this->type;  
  }  
  
  public function getName()
  {
    return $this->name;
  }

}
