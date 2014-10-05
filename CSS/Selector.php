<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\CSS;

/**
 * Define the selector section of a CSS Rule
 *
 */
class Selector
{
  /**
   * The style selector groups
   *
   * @var array
   */
  private $groups;

  /**
   * Determines if the output of this class should be stripped of extra white
   * space that isn't needed.
   *
   * @var boolean
   */
  private $compress;

  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    $this->groups = array();
  }

  /**
   * Produce the selector output
   *
   * @return string
   */
  public function output()
  {
    if ( empty($this->groups) )
    {
      return '';
    }

    if ( $this->compress )
    {
      $rtn = implode(',', $this->groups);
    }
    else
    {
      $rtn = implode(', ', $this->groups);
    }

    return $rtn;
  }

  /**
   * Sets a new selector text, replacing anything already defined
   *
   * @param string
   * @return this
   */
  public function setSelector($selText)
  {
    $this->groups = array();

    $this->addSelector($selText);

    return $this;
  }

  /**
   * Adds a new selector to the comma separated group.
   *
   * @param string
   * @return this
   */
  public function addSelector($selText)
  {
    $this->groups[] = trim($selText);

    return $this;
  }

  /**
   * Sets the flag to compress the output as much as possible
   *
   * @param boolean
   * @return this
   */
  public function setCompress($flag)
  {
    if ( $flag )
    {
      $this->compress = true;
    }
    else
    {
      $this->compress = false;
    }

    return $this;
  }
}
