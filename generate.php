<?php
require 'ClassLoader.php';

$cl = new Wutil\Classloader();
$cl->register();


$config = new \Wutil\Configuration('config.ini');

foreach($config->getSection('wsdl') as $name => $wsdl) {
  $generator = new \Wutil\Generator\ClassGenerator($wsdl);
  $generator->generate('SOAP/', $name,  $config->get('settings', 'namespace', 'Wuti\SOAP'), $config->get('settings', 'inherit', 'SoapClient'), $config->get('settings', 'tempate', 'SoapClass'));
}

