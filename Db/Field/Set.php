<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * A set of field type definitions meant to provide an interface to type objects
 * for a Table class.
 */
class Set
{
  /**
   * List of Field objects
   *
   * @var array
   */
  private $fields;

  /**
   * Initialize the Set object
   *
   * @param object
   */
  public function __construct()
  {
    $this->fields = array();
  }

  /**
   * Diagnostic output of the fields in this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = '<pre>'.print_r($this->fields, true).'</pre>';

    return $rtn;
  }

  /**
   * Provide a slightly easier way to get to the field type definitions
   *
   * @param string Name of the field
   * @return \Metrol\Db\Field
   */
  public function __get($fieldName)
  {
    return $this->getField($fieldName);
  }

  /**
   * Add a field type definition to the list
   *
   * @param \Metrol\Db\Field
   */
  public function add(\Metrol\Db\Field $field)
  {
    $this->fields[$field->getFieldName()] = $field;
  }

  /**
   * Delete a field type from the set
   *
   * @param string Field Name
   */
  public function delete($fieldName)
  {
    if ( !$this->exists($fieldName) )
    {
      return;
    }

    unset($this->fields[$fieldName]);
  }

  /**
   * Provide a named field type, if it exists
   *
   * @param string Field Name
   * @return \Metrol\Db\Field
   */
  public function getField($fieldName)
  {
    if ( $this->exists($fieldName) )
    {
      return $this->fields[$fieldName];
    }
  }

  /**
   * Does the field exist in the set?
   *
   * @param string
   * @return boolean
   */
  public function exists($fieldName)
  {
    if ( array_key_exists($fieldName, $this->fields) )
    {
      return true;
    }

    return false;
  }
}
