<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Action;

/**
 * Defines a Controller class and Action
 *
 */
class Controller
{
  /**
   * The name of this action.
   *
   * @var string
   */
  protected $name;

  /**
   * List of class names of invokable Controllers
   *
   * @var array
   */
  protected $classes;

  /**
   * List of actions within a Controller that needs called indexed by the
   * controller classes array
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
   * Initializes the Action Definition
   *
   */
  public function __construct()
  {
    $this->name = '';

    $this->classes   = array();
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
    $rtn  = "\n".'Action Definition Name: ['.$this->name."]\n";
    $rtn .= "------------------------------------\n";

    if ( count($this->classes) == 0 )
    {
      $rtn .= "  No controllers or actions defined\n";

      return $rtn;
    }

    foreach ( $this->classes as $classIdx => $class )
    {
      $rtn .= '  Controller: '.$class."\n";

      if ( !array_key_exists($classIdx, $this->actions) )
      {
        continue;
      }

      foreach ( $this->actions[$classIdx] as $action )
      {
        $rtn .= '      Action: '.$action;

        if ( count($this->arguments) == 0 )
        {
          $rtn .= "()\n";
        }
        else
        {
          $argStr = "( array(";

          foreach ( $this->arguments as $arg )
          {
            $argStr .= $arg.', ';
          }

          $rtn .= substr($argStr, 0, -2).") )\n";
        }
      }
    }

    return $rtn;
  }

  /**
   * Sets the name of this definition
   *
   * @param string $definitionName Name of the action definition
   *
   * @return this
   */
  public function setName($definitionName)
  {
    $this->name = $definitionName;

    return $this;
  }

  /**
   * Provide the name of this action definition
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Provide the list of controllers
   *
   * @return array
   */
  public function getControllers()
  {
    return $this->classes;
  }

  /**
   * Provide the actions for the specified controller index
   *
   * @param integer $controllerIndex Which controller to pull actions from
   *
   * @return array
   */
  public function getActions($controllerIndex)
  {
    $rtn = array();

    $i = intval($controllerIndex);

    if ( array_key_exists($i, $this->actions) )
    {
      $rtn = $this->actions[$i];
    }

    return $rtn;
  }

  /**
   * Provide the list of arguments to be applied to the actions
   *
   * @return array
   */
  public function getArguments()
  {
    return $this->arguments;
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
   * Adds a new Controller::Action to the stack
   *
   * @param string $controllerClass Fully qualified class name (FQCN)
   * @param string $action Name of the action method to call
   *
   * @return this
   */
  public function addAction($controllerClass, $action)
  {
    $controllerClass = str_replace('/', '\\', $controllerClass);
    $controllerClass = str_replace('.', '\\', $controllerClass);
    $controllerClass = str_replace('_', '\\', $controllerClass);

    $classIdx = array_search($controllerClass, $this->classes);

    // Add the class as needed
    if ( $classIdx === false )
    {
      $classIdx = count($this->classes);
      $this->classes[$classIdx] = $controllerClass;
    }

    // Add the action as needed, relate it to the class
    if ( array_key_exists($classIdx, $this->actions) )
    {
      if ( !in_array($action, $this->actions[$classIdx]) )
      {
        $this->actions[$classIdx][] = $action;
      }
    }
    else
    {
      $this->actions[$classIdx][] = $action;
    }

    return $this;
  }

  /**
   * Add arguments that will be applied to all the actions specified in this
   * definition.
   *
   * @param Argument values to be passed
   *
   * @return this
   */
  public function addArguments()
  {
    if ( func_num_args() == 0 )
    {
      return;
    }

    foreach ( func_get_args() as $argVal )
    {
      $this->arguments[] = $argVal;
    }

    return $this;
  }
}
