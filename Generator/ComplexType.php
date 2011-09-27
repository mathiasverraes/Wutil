<?php
namespace Wutil\Generator;

/**
 * This class describes a complex type 
 *
 * @author Moritz Schwoerer <moritz.schwoerer@gmail.com>
 */
class ComplexType
{
  protected $name;
  protected $members;
  
  public function __construct($name, array $members = array())
  {
    $this->name = $name;
    $this->members = $members;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getMembers()
  {
    return $this->members;
  }
}