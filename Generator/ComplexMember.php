<?php
namespace Wutil\Generator;

/**
 * This class describes a member variable of the complex type
 *
 * @author Moritz Schwoerer <moritz.schwoerer@gmail.com>
 */
class ComplexMember
{
  protected $name;
  protected $type;
  protected $docu;
  protected $min;
  protected $max;

  public function getMin()
  {
    return $this->min;
  }

  public function setMin($min)
  {
    $this->min = $min;
  }

  public function getMax()
  {
    return $this->max;
  }

  public function setMax($max)
  {
    $this->max = $max;
  }

  
  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getType()
  {
    return $this->type;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function getDocu()
  {
    return $this->docu;
  }

  public function setDocu($docu)
  {
    $this->docu = $docu;
  }



}