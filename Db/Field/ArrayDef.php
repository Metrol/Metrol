<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * Provide type handling for Array field types
 */
class ArrayDef
  extends \Metrol\Data\Type\ArrayDef
  implements \Metrol\Db\Field
{
  /**
   * Initialize the Array definition object
   *
   * @param \Metrol\Db\Field
   */
  public function __construct(\Metrol\Db\Field $fieldType)
  {
    parent::__construct($fieldType);
  }

  /**
   * Used when a Record object is looking to set a value
   *
   * @param array
   * @return array
   */
  public function setValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return null;
    }

    if ( !is_array($value) )
    {
      if ( strlen($value) > 0 )
      {
        $value = array($value);
      }
      else if ( substr($value, 0, 1) == '{' )
      {
        $value = $this->parseStringToArray($value);
      }
      else
      {
        $value = array();
      }
    }

    return $this->boundsValue($value);
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

    if ( !is_array($value) )
    {
      return "{}";
    }

    $rtn = $this->parseArrayToString($value);

    return $rtn;
  }

  /**
   * Takes the expected format of an array from the database and converts it
   * into a PHP array.
   *
   * @param string
   * @return array
   */
  private function parseStringToArray($value)
  {
    // Getting to this soon
  }

  /**
   * Takes a PHP array and turns it into the format the database can digest
   *
   * @param array
   * @return string
   */
  private function parseArrayToString(array $value)
  {
    // Getting to this soon
  }
}
