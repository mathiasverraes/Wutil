<?php
namespace Wutil\Generator;

use DomNode;
use DOMDocument;

/**
 * This class describes a member variable of the complex type
 *
 * @author Moritz Schwoerer <moritz.schwoerer@gmail.com>
 */
class ComplexMethod
{
  protected $name;

  protected $params = array();

  protected $return;

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getParams()
  {
    return $this->params;
  }

  public function getParameters()
  {
    return $this->params;
  }

  public function setParams($params)
  {
    $this->params = $params;
  }

  public function getReturn()
  {
    return $this->return;
  }

  public function setReturn($return)
  {
    $this->return = $return;
  }

  public static function createFromDom(DomNode $dom, DOMDocument $context)
  {
    $self = new self();
    $self->setName($dom->getAttribute('name'));

    if ($dom->getElementsByTagName('input')->item(0)) {
      $dom->getElementsByTagName('input')->item(0)->getAttribute('message');
    }
    
    return $self;
  }


}