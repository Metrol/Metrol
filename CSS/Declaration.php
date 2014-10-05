<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\CSS;

/**
 * Define the declaration of assigning a value to a style property
 *
 */
class Declaration
{
  /**
   * The property getting a value
   *
   * @var string
   */
  private $property;

  /**
   * The value to assign the property
   *
   * @var string
   */
  private $value;

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
    $this->property = '';
    $this->value    = '';
  }

  /**
   * Produce the declaration output
   *
   * @return string
   */
  public function output()
  {
    if ( strlen($this->property) == 0 or strlen($this->value) == 0 )
    {
      return '';
    }

    if ( $this->compress )
    {
      $rtn = $this->property.':'.$this->value.';';
    }
    else
    {
      $rtn = $this->property.': '.$this->value.';';
    }

    return $rtn;
  }

  /**
   * Sets the property with a value
   *
   * @param string
   * @param string
   * @return this
   */
  public function setProperty($prop, $val)
  {
    $this->property = trim($prop);
    $this->value    = trim($val);

    return $this;
  }

  /**
   * Sets the flag to compress the output as much as possible
   *
   * @param boolean
   *
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
