<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Action;

/**
 * A catalog of system Action Definitions
 *
 */
class Catalog implements \Iterator \Countable
{
  /**
   * The list of action objects
   *
   * @var array
   */
  protected $actions;

  /**
   * Initializes the Action Catalog
   *
   */
  public function __construct()
  {
    $this->actions = array();
  }

  /**
   * Add an Action definition to the stack, indexed by the name of the action
   *
   * @param \Metrol\Action\Controller $actDef
   */
  public function addActionDefinition(\Metrol\Action\Controller $actDef)
  {
    $this->actions[ $action->getName() ] = $actDef;
  }

  /**
   * Provide a given action definition by name.
   * Returns NULL if not found.
   *
   * @param string $actionName Name of the action definition
   * @return \Metrol\Action\Controller
   */
  public function getActionDef($actionName)
  {
    $rtn = null;

    if ( array_key_exists($actionName, $this->actions) )
    {
      $rtn = $this->actions[$actionName];
    }

    return $rtn;
  }

  /**
   * Provide a new action definition
   *
   * @return \Metrol\Action\Controller
   */
  public function getNewActionDef()
  {
    return new \Metrol\Action\Controller;
  }

  /**
   * Report how many action definitions we've got in here
   *
   * @return integer
   */
  public function count()
  {
    return count($this->actions);
  }

  /**
   * Reverses the order of all the action definitions in this set.
   *
   */
  public function reverse()
  {
    $this->actions = array_reverse($this->actions, true);
  }

  /**
   * Implementing the Iterartor interface to walk through the action definitions
   *
   */
  public function rewind()
  {
    reset($this->actions);
  }

  public function current()
  {
    return current($this->actions);
  }

  public function key()
  {
    return key($this->actions);
  }

  public function next()
  {
    return next($this->actions);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
