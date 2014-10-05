<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * Provide type, bounds, and formatting for a String database field
 */
class String
  extends \Metrol\Data\Type\String
  implements \Metrol\Db\Field
{
  /**
   * Keep a copy of the db driver so this knows where to call the escape string
   *
   * @var \Metrol\Db\Driver
   */
  protected $driver;

  /**
   * Initialize the String object
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Used when a Record object is looking to set a value
   *
   * @param string
   * @return string
   */
  public function setValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return null;
    }

    return $this->boundsValue($value);
  }

  /**
   * Sets the DB driver this will use when running strings through an
   * escape routine.
   *
   * @param \Metrol\Db\Driver
   */
  public function setDbDriver(\Metrol\Db\Driver $driver)
  {
    $this->driver = $driver;
  }

  /**
   * Provide a properly quoted and escaped representation of the data ready for
   * putting into an SQL statement.
   *
   * @param mixed
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
      return "''";
    }

    $rtn = $this->driver->escapeString($value);
    $rtn = $this->boundsValue($rtn);

    $rtn = "'".$rtn."'";

    return $rtn;
  }
}
