<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Describes an Enumerated field and it's allowed values
 */
class Enumerated extends \Metrol\Data\Type
{
  /**
   * Allowed values for the type
   *
   * @var array
   */
  protected $values;

  /**
   * Instantiate the Integer description
   */
  public function __construct()
  {
    parent::__construct();

    $this->values = array();
  }

  /**
   * Add a new allowed value
   *
   * @param string Allowed value
   */
  public function addValue($value)
  {
    // Only accept the first 63 chars
    $this->values[] = substr($value, 0, 63);
  }

  /**
   * Provide the list of allowed values
   *
   * @return array
   */
  public function getValues()
  {
    return $this->values;
  }

  /**
   * Provide a list of values with keys equal to the value.
   * Handy for drop down lists and such.
   *
   * @return array
   */
  public function getKeyedValues()
  {
    $rtn = array();

    foreach ( $this->values as $val )
    {
      $rtn[$val] = $val;
    }

    return $rtn;
  }

  /**
   * Check to see if the value being passed in is allowed
   *
   * @param string
   * @return booean
   */
  public function isAllowed($value)
  {
    if ( in_array($value, $this->values) )
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Return the first value in the list as the default
   *
   * @return string
   */
  public function defaultValue()
  {
    reset($this->values);

    return current($this->values);
  }
}
