<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\DSN;

/**
 * Set of DSN definitions for connecting to data resources
 *
 */
class Set implements \Iterator
{
  /**
   * The list of DSN objects
   *
   * @var array
   */
  protected $dsnSet;

  /**
   * Initializes the List of DSNs
   *
   * @param object
   */
  public function __construct()
  {
    $this->dsnSet = array();
  }

  /**
   * Add a DSN object to the stack, indexed by the name of the DSN
   *
   * @param \Metrol\Db\DSN
   *
   * @return this
   */
  public function addDSN(\Metrol\Db\DSN $dsn)
  {
    $this->dsnSet[ $dsn->resourceName ] = $dsn;

    return $this;
  }

  /**
   * Provide a given dsn be name.
   * Returns NULL if not found.
   *
   * @param string Name of dsn
   *
   * @return \Metrol\Db\DSN
   */
  public function getDSN($dsnName)
  {
    $rtn = null;

    if ( array_key_exists($dsnName, $this->dsnSet) )
    {
      $rtn = $this->dsnSet[$dsnName];
    }

    return $rtn;
  }

  /**
   * Report how many items we've got in here
   *
   * @return integer
   */
  public function count()
  {
    return count($this->dsnSet);
  }

  /**
   * Implementing the Iterartor interface to walk through the dsnSet
   *
   */
  public function rewind()
  {
    reset($this->dsnSet);
  }

  public function current()
  {
    return current($this->dsnSet);
  }

  public function key()
  {
    return key($this->dsnSet);
  }

  public function next()
  {
    return next($this->dsnSet);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
