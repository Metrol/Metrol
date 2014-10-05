<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Log\Writer;

/**
 * Maintains a set of Log Writers
 *
 */
class Set implements \Iterator, \Countable
{
  /**
   * The list of log writers
   *
   * @var array
   */
  protected $writers;

  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    $this->writers = array();
  }

  /**
   * Add a log writer to the stack
   *
   * @param \Metrol\Log\Writer
   */
  public function addWriter(\Metrol\Log\Writer $writer)
  {
    $this->writers[] = $writer;
  }

  /**
   * How many writers are in the stack
   *
   * @return integer Count of the writers
   */
  public function count()
  {
    return count($this->writers);
  }

  /**
   * Implementing the Iterartor interface to walk through the writers
   */
  public function rewind()
  {
    reset($this->writers);
  }

  public function current()
  {
    return current($this->writers);
  }

  public function key()
  {
    return key($this->writers);
  }

  public function next()
  {
    return next($this->writers);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
