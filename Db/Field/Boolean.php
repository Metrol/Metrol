<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * Description of Boolean field
 */
class Boolean
  extends \Metrol\Data\Type\Boolean
  implements \Metrol\Db\Field
{
  /**
   * Initialize the Integer object
   *
   * @param object
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Called when a record is setting a value
   * If the value isn't recognized, the default is to return FALSE
   *
   * @param boolean|string
   * @return boolean
   */
  public function setValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return null;
    }
    else if ( $value === null and !$this->nullOk )
    {
      return false;
    }

    if ( is_bool($value) )
    {
      return $value;
    }

    $v = strtolower($value);

    if ( $v == 't' or $v == 'true' or $v == '1' or $v == 'on' )
    {
      return true;
    }

    if ( $v == 'f' or $v == 'false' or $v == '0' or $v == 'off' )
    {
      return false;
    }

    return false;
  }

  /**
   * Provide the SQL ready value of the input
   *
   * @param boolean
   * @return string
   */
  public function getSQLValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return 'null';
    }
    else if ( $value === null and !$this->nullOk )
    {
      return 'FALSE';
    }

    if ( $value )
    {
      $rtn = 'TRUE';
    }
    else
    {
      $rtn = 'FALSE';
    }

    return $rtn;
  }
}
