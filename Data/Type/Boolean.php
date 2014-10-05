<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Describes a boolean type
 */
class Boolean extends \Metrol\Data\Type
{
  /**
   * Instantiate the Integer description
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Makes sure the value being returned is always a boolean TRUE or FALSE
   *
   * @param mixed
   * @return boolean
   */
  public function boundsValue($value)
  {
    if ( $value )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}
