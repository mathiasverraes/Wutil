<?php
namespace Wutil;

class Classloader
{
  private $namespace;
  private $path;

  /**
   * Installs this class loader on the SPL autoload stack.
   */
  public function register()
  {
    spl_autoload_register(array($this, 'loadClass'));
  }

  /**
   * Uninstalls this class loader on the SPL autoload stack.
   */
  public function unregister()
  {
    spl_autoload_unregister(array($this, 'loadClass'));
  }

  /**
   * Loads the given class or interface.
   *
   * @param string $classname The name of the class to load.
   * @return boolean TRUE if the class has been successfully loaded, FALSE otherwise.
   */
  public function loadClass($className)
  {
    if (strpos($className, 'Wutil') === false) {
      return false;
    }
    $className = str_replace('Wutil\\', '', $className);

    $path = __DIR__.'/'.str_replace('\\', '/', $className).'.php';

    require_once $path;
    return true;
  }
}