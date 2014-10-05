<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Action\Catalog;

/**
 * Used to store the list of available action definitions at run time
 *
 */
class Manager
{
  /**
   * The singleton instance of this class
   *
   * @var \Metrol\Action\Catalog\Manager
   */
  static protected $thisObj;

  /**
   * List of registered routes
   *
   * @var \Metrol\Action\Catalog
   */
  protected $actionCatalog;

  /**
   * Instantiate the catalog manager
   *
   */
  protected function __construct()
  {
    $this->initCatalog();
  }

  /**
   * Diagnostic output to show what all is going on with all of the defined
   * actions.
   *
   * @return string
   */
  public function __toString()
  {
    $o = static::getInstance();

    $rtn  = "Catalog Manager: All Defined Controller::Actions\n";
    $rtn .= '  -- '.get_class($o)." --\n";
    $rtn .= "-----------------------------------------------\n";

    foreach ( $o->actionCatalog as $actDef )
    {
      $rtn .= $actDef;
    }

    return $rtn;
  }

  /**
   * Provide the one and only instance of this class
   *
   * @return \Metrol\Action\Catalog\Manager
   */
  static public function getInstance()
  {
    if ( !is_object(static::$thisObj) )
    {
      $className = __CLASS__;
      static::$thisObj = new $className;
    }

    return static::$thisObj;
  }

  /**
   * Provide the Action Catalog being stored here
   *
   * @return \Metrol\Action\Catalog
   */
  static public function getCatalog()
  {
    $catMgr = static::getInstance();

    return $catMgr->actionCatalog;
  }

  /**
   * Get the action catalog instantiated
   *
   */
  protected function initCatalog()
  {
    $this->actionCatalog = new \Metrol\Action\Catalog;
  }
}
