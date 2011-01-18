<?php
namespace Wutil;

class Configuration
{
  protected $config;

  /**
   *
   * @param string $config_file
   */
  public function __construct($config_file)
  {
    $this->config = parse_ini_file($config_file, true);

    foreach($this->config as $key => $value) {
      unset($this->config[$key]);
      $this->config[strtoupper($key)] = $value;
    }

  }

  public function get($section, $get, $default = null)
  {
    $section = $this->getSection($section);
    return isset($section[$get]) ? $section[$get] : $default;
  }

  public function getSection($section)
  {
    return $this->config[strtoupper($section)];
  }
}