<?php
namespace Wutil\Generator;

use SoapClient;
use DOMDocument;
use DOMElement;

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
   * Output directory
   * @var string
   */
  protected $output;

  /**
   * 
   * @param string $wsdl
   */
  public function __construct($wsdl)
  {
    $this->wsdl = $wsdl;
  }

  public function setOutputDirectory($dir)
  {
    $this->output = $dir;
  }

  public function getOutputDirectory($dir)
  {
    return $this->output;
  }

  /**
   *
   * @param string $target_file
   */
  public function generate($target_dir, $class_name, $namespace, $inherit)
  {
    $dom = new DOMDocument;
    $dom->load($this->wsdl);

    print_r(file_get_contents($this->wsdl));
    foreach($dom->getElementsByTagName('complexType') as $complexType) {
      $complexType = $this->parseComplexType($complexType);
      $class_name = $complexType->getName();

      $vars = array(
          'members' => $complexType->getMembers(),
          'class' => $complexType->getName(),
          'namespace' => $namespace
      );

      file_put_contents($target_dir.'/'.$class_name.'.php', $this->fetchTemplate(__DIR__.'/../Templates/ComplexType.php', $vars));
    }


    $vars = array(
        'methods' => array(),
        'class' => $class_name,
        'namespace' => $namespace,
        'inherit' => $inherit,
        'wsdl' => $this->wsdl
    );

    foreach ($dom->getElementsByTagName('operation') as $operation) {
      $vars['methods'][] = ComplexMethod::createFromDom($operation, $dom);
    }

    file_put_contents($target_dir.'/'.$class_name.'.php', $this->fetchTemplate(__DIR__.'/../Templates/SoapClass.php', $vars));
  }

  protected function parseComplexType(DOMElement $complexType)
  {
    $members = array();
    
    if ($complexType->getElementsByTagName('all')->length) {
    
    foreach($complexType->getElementsByTagName('all')->item(0)->getElementsByTagName('element') as $memberDom) {
      $member = new ComplexMember;
      $member->setName($memberDom->getAttribute('name'));
      $type = explode(':', $memberDom->getAttribute('type'));
      $type = array_pop($type);
      $member->setType($type);
      if ($memberDom->getAttribute('minOccurs')) {
        $member->setMin($memberDom->getAttribute('minOccurs'));
      }
      if ($memberDom->getAttribute('maxOccurs')) {
        $member->setMin($memberDom->getAttribute('maxOccurs'));
      }

      $members[] = $member;
    }
    }
    return new ComplexType($complexType->getAttribute('name'), $members);
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
