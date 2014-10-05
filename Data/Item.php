<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data;

/**
 * Represents a set of data fields similar to a record in a database.  Values
 * may be stored and retrieved from here, but there is no capability of saving
 * this data.
 *
 */
class Item implements \Iterator, \JsonSerializable, \Countable
{
  /**
   * All of the data of this data result keyed on the field name
   *
   * @var array
   */
  protected $dataItem;

  /**
   * Initializes the member variables
   *
   */
  public function __construct()
  {
    $this->dataItem = array();
  }

  /**
   * Provide seemless access to getting field values by name
   *
   * @param string Field name being requested
   */
  public function __get($field)
  {
    return $this->getValue($field);
  }

  /**
   * Sets a field's value as though it were a member variable
   *
   * @param string Field name to set
   * @param mixed
   */
  public function __set($field, $value)
  {
    $this->setValue($field, $value);
  }

  /**
   * Determines if the requested object property exists.
   *
   * @param string Name of the propery
   * @return boolean
   */
  public function __isset($field)
  {
    return $this->isFieldSet($field);
  }

  /**
   * Produces a diagnostic output showing what is stored in this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = 'Diagnostic output from '.get_class($this)."\n";
    $rtn .= 'Data Item Contents:'."\n";

    $rtn .= $this->debug();

    $rtn = '<pre>'.$rtn.'</pre>';

    return $rtn;
  }

  /**
   * Produce a diagnostic output showing the values stored here
   *
   * @return string
   */
  public function debug()
  {
    $rtn = '';

    foreach ( $this->dataItem as $field => $value )
    {
      if ( is_bool($value) )
      {
        $rtn .= $field.' = ';

        if ( $value )
        {
          $rtn .= 'TRUE';
        }
        else
        {
          $rtn .= 'FALSE';
        }
      }
      else if ( is_array($value) )
      {
        $rtn .= $field.' = '.print_r($value, true);
      }
      else
      {
        $rtn .= $field.' = '.$value;
      }

      if ( is_object($value) )
      {
        $rtn .= ' ('.get_class($value).')';
      }

      $rtn .= "\n";
    }

    return $rtn;
  }

  /**
   * Provide a JSON ready array of all the values that have been stored here.
   *
   * @return array
   */
  public function jsonSerialize()
  {
    return $this->dataItem;
  }

  /**
   * Sets the value of a field for this data item
   *
   * @param string Field name to set
   * @param mixed
   */
  public function setValue($field, $value)
  {
    $this->dataItem[$field] = $value;
  }

  /**
   * Provides the value of a field
   *
   * @param string Field name being requested
   * @return mixed
   */
  public function getValue($field)
  {
    if ( !$this->isFieldSet($field) )
    {
      return null;
    }

    return $this->dataItem[$field];
  }

  /**
   * Remove a specific field from the set entirely
   *
   * @param string Field name to remove
   * @return this
   */
  public function unsetField($fieldName)
  {
    if ( $this->isFieldSet($fieldName) )
    {
      unset($this->dataItem[$fieldName]);
    }

    return $this;
  }

  /**
   * Clears out all the values stored in this object
   *
   * @return this
   */
  public function resetValues()
  {
    $this->dataItem = array();

    return $this;
  }

  /**
   * Determines if the requested object property exists.
   *
   * @param string Name of the propery
   * @return boolean
   */
  public function isFieldSet($field)
  {
    if ( array_key_exists($field, $this->dataItem) )
    {
      return true;
    }

    return false;
  }

  /**
   * Provide the entire array of values back to the caller
   *
   * @return array
   */
  public function getValueArray()
  {
    return $this->dataItem;
  }

  /**
   * Provide the list of fields for this data item
   *
   * @return array
   */
  public function getFields()
  {
    return array_keys($this->dataItem);
  }

  /**
   * Provides how many fields exist in this item.  Also supports the countable
   * interface implementation.
   *
   * @return integer
   */
  public function count()
  {
    return count($this->dataItem);
  }

  /**
   * Implementing the Iterartor interface to walk through each field
   *
   */
  public function rewind()
  {
    reset($this->dataItem);
  }

  public function current()
  {
    return current($this->dataItem);
  }

  public function key()
  {
    return key($this->dataItem);
  }

  public function next()
  {
    return next($this->dataItem);
  }

  public function valid()
  {
    $rtn = true;
    $key = $this->key();

    if ( $key === null or $key === false )
    {
      $rtn = false;
    }

    return $rtn;
  }
}
