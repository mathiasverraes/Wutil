<?php
/**
 * This class is able read and analyze WSDL files
 *
 * @author Moritz Schwoerer <moritz.schwoerer@adition.com>
 * @since 2011/01/03
 */
class WSDLReader
{
  protected $xml;

  public function __construct(DOMDocument $xml)
  {
    $this->xml = $xml;
  }

  /**
   *
   * @return array
   */
  public function getRpcMessages()
  {
    $messages = array();
    foreach($this->xml->getElementsByTagName('message') as $message) {
      $messages[$message->getAttribute('name')] = array();
      foreach($message->getElementsByTagName('part') as $part) {
        $messages[$message->getAttribute('name')][$part->getAttribute('name')] = $part->getAttribute('type');
      }
    }

    return $messages;
  }

  /**
   *
   * @return array
   */
  public function getOperations()
  {
    $operations = array();

    foreach($this->xml->getElementsByTagName('wsdl:portType') as $port) {
      foreach($port->getElementsByTagName('wsdl:operation') as $operations) {
        $operations[] = $operations->getAttribute('name');
      }
    }

    foreach($this->xml->getElementsByTagName('portType') as $port) {
      foreach($port->getElementsByTagName('operation') as $operation) {
        $operations[] = $operation->getAttribute('name');
      }
    }

    return $operations;
  }

  /**
   * Gets the document message parameters
   *
   * @return array
   */
  public function getDocuMessages()
  {
    $messages = array();
    //all the element
    $elements = $this->xml->getElementsByTagName('element');

    foreach($this->xml->getElementsByTagName('message') as $message) {
      $messages[$message->getAttribute('name')] = array();
      foreach($message->getElementsByTagName('part') as $part) {
        if($part->getAttribute('name')=='parameters') {
          $msgName = $part->getAttribute('element');
          $msgName = str_replace('tns:','',$msgName);
          foreach($elements as $element) {
            if ($element->getAttribute('name') == $msgName) {
              foreach ($element->getElementsByTagName('element') as $param) {
                $messages[$message->getAttribute('name')][$param->getAttribute('name')] = $param->getAttribute('type');
              }
              break;
            }
          }
        }
      }
    }

    return $messages;
  }
  
  /**
   * 
   */
  public function getURI() {
    return $this->xml->documentURI;
  }
}


