<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Route;

/**
 * Defines a Controller class and methods to be called as an action for a route
 *
 */
class Action
{
  /**
   * List of class name of the invokable Controller
   *
   * @var string
   */
  protected $controlClass;

  /**
   * List of methods within a Controller that need to be called
   *
   * @var array
   */
  protected $methodSet;

  /**
   * Initializes the Action Definition
   *
   */
  public function __construct()
  {
    $this->name = '';

    $this->controlClass = '';
    $this->methodSet    = array();
  }

  /**
   * Diagnostic output for the contents of this object
   *
   * @return string
   */
  public function __toString()
  {
    $error = false;
    $rtn  = "<br />\n";
    $rtn .= '  Action Definition from '.get_class($this)."\n";
    $rtn .= "  ------------------------------------\n";

    if ( strlen($this->controlClass) == 0 )
    {
      $rtn .= "  No controller defined\n";

      $error = true;
    }

    if ( count($this->methodSet) == 0 )
    {
      $rtn .= "  No methods defined\n";

      $error = true;
    }

    if ( $error )
    {
      return $rtn;
    }

    $rtn .= '    Controller: '.$this->controlClass."<br />\n";

    foreach ( $this->methodSet as $method )
    {
      $rtn .= '        Method: '.$method."<br />\n";
    }

    return $rtn;
  }

  /**
   * Sets the class to be instantiated
   *
   * @param string $className Name of the controller class
   *
   * @return this
   */
  public function setClass($className)
  {
    $className = str_replace('/', '\\', $className);
    $className = str_replace('.', '\\', $className);
    $className = str_replace('_', '\\', $className);

    $this->controlClass = $className;

    return $this;
  }

  /**
   * Puts a new method on the stack to be called
   *
   * @param string $method Name of the method in the Controller Class to call
   *
   * @return this
   */
  public function addMethod($method)
  {
    if ( !in_array($method, $this->methodSet) )
    {
      $this->methodSet[] = $method;
    }
  }

  /**
   * Instantiates the Controller object and then calls each of the methods
   * with the supplied arguments.
   *
   * @param array $args
   */
  public function run(array $args = null)
  {
    $cc  = $this->controlClass;
    $obj = new $cc;

    foreach ( $this->methodSet as $method )
    {
      $obj->$method($args);
    }
  }
}
