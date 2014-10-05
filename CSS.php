<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * A wrapper class for all the parts that make up a cascading style sheet
 *
 */
class CSS
{
  /**
   * The list of rules that make up the style sheet
   *
   * @var array
   */
  private $rules;

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
    $this->rules    = array();
    $this->compress = false;
  }

  /**
   * Provide an output for print or echo
   *
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * Produce the complete set of CSS rules as a string output
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    foreach ( $this->rules as $rule )
    {
      $rule->setCompress($this->compress);

      $rtn .= $rule->output();
    }

    return $rtn;
  }

  /**
   * Provide a new CSS Rule object that is automatically added to the stack of
   * rules
   *
   * @return \Metrol\CSS\Rule
   */
  public function getNewRule()
  {
    $rule = new \Metrol\CSS\Rule;

    $this->rules[] = $rule;

    return $rule;
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
