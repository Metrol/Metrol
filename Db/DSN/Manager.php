<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\DSN;

/**
 * Used to store the list of available DSNs at run time
 *
 */
class Manager
{
  /**
   * The singleton instance of this class
   *
   * @var this
   */
  static protected $thisObj;

  /**
   * List of DSN objects
   *
   * @var \Metrol\Db\DSN\Set
   */
  protected $dsnSet;

  /**
   * Initializes the DSN manager
   *
   * @param object
   */
  protected function __construct()
  {
    $this->initDSNSet();
  }

  /**
   * Provide the one and only instance of this class
   *
   * @return \Metrol\Db\DSN\Manager
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
   * Add a new DSN to the set
   *
   * @param \Metrol\Db\DSN
   */
  public function addDSN(\Metrol\Db\DSN $dsn)
  {
    $this->dsnSet->addDSN($dsn);
  }

  /**
   * Provide back the list of DSNs object
   *
   * @return \Metrol\Db\DSN\Set
   */
  public function getDSNSet()
  {
    return $this->dsnSet;
  }

  /**
   * Get a DSN object based on the name
   *
   * @param string Name of the DSN
   *
   * @return \Metrol\Db\DSN
   */
  public function getDSN($dsnName)
  {
    return $this->dsnSet->getDSN($dsnName);
  }

  /**
   * Get the list of routes set together
   *
   */
  protected function initDSNSet()
  {
    $this->dsnSet = new Set;
  }
}
