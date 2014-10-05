<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db;

/**
 * Defines the API that Field Type definition classes must support
 */
interface Field
{
  /**
   * Provide a properly quoted and escaped representation of the data ready for
   * putting into an SQL statement.
   *
   * @param mixed
   * @return string
   */
  public function getSQLValue($value);

  /**
   * Makes sure the value being returned is within the specified values allowed
   * for the field type
   *
   * @param string
   * @return string
   */
  public function boundsValue($value);

  /**
   * Used when a Record object is looking to set a value
   *
   * @param mixed
   * @return mixed
   */
  public function setValue($value);
}
