namespace <?php print $namespace ?>;
use <?php print $inherit ?> as MySoapClient;

class <?php print $class; ?> extends MySoapClient
{
  protected $expected;

<?php foreach($methods as $method): ?>
  /**
   * Calls the <?php print $method->getName(); ?> Method
<?php foreach($method->getParameters() as $parameter): ?>
   * @param type $<?php print $parameter->getName() ?> 
<?php endforeach; ?>
   *
   * @return <?php print $method->getReturn() ?> 
   */
  public function <?php print $method->getName(); ?>(<?php print implode(', ', $method->getInvokeArgs()); ?>)
  {
    $this->expected = '<?php print $method->getReturn() ?>';
    return $this->__soapCall('<?php print $method->getName(); ?>', func_get_args());
  }
  
<?php endforeach; ?>

  /**
   * location of the wsdl
   * @return string
   */
  public function getWSDL()
  {
    return '<?php print $wsdl ?>';
  }

}