<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Defines a Controller class and Action to be used by the Dispatcher
 *
 */
class Route
{
  /**
   * The name of this route.
   *
   * @var string
   */
  protected $name;

  /**
   * List of actions associated with this route
   *
   * @var array
   */
  protected $actions;

  /**
   * List of arguments to be applied to each action.
   *
   * @var array
   */
  protected $arguments;

  /**
   * Initializes the Route object
   *
   * @param string Name of this route
   */
  public function __construct($routeName)
  {
    $this->name = $routeName;

    $this->actions   = array();
    $this->arguments = array();
  }

  /**
   * Diagnostic output for the contents of this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn  = "\n".'Route Name: ['.$this->name."]\n";
    $rtn .= "------------------------------------\n";

    if ( count($this->classes) == 0 )
    {
      $rtn .= "  No controllers or actions defined\n";

      return $rtn;
    }

    foreach ( $this->actions as $action )
    {
      $rtn .= $action;
   }

    return $rtn;
  }

  /**
   * Calls all the actions with the available arguments for this route
   *
   */
  public function run()
  {
    foreach ( $this->actions as $action )
    {
      $action->run($this->arguments);
    }
  }

  /**
   * Provide the name of this route
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Used to insure no prior arguments are on the list.  Clean slate.
   *
   * @return this
   */
  public function clearArguments()
  {
    $this->arguments = array();

    return $this;
  }

  /**
   * Add a new Action to the route
   *
   * @param \Metrol\Frame\Route\Action $action
   *
   * @return this
   */
  public function addAction(\Metrol\Frame\Route\Action $action)
  {
    $this->actions[] = $action;
  }

  /**
   * Add an argument that will be applied to all the actions specified in this
   * route.
   *
   * @param Argument value to be passed to the call for action
   *
   * @return this
   */
  public function addArgument($argValue)
  {
    $this->arguments[] = $argValue;

    return $this;
  }
}
