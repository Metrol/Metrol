<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Item;

/**
 * Extends the Db Item to defines a read/write record for a database table
 *
 */
abstract class Record extends \Metrol\Db\Item
{
  /**
   * The value of the primary key
   *
   * @var integer
   */
  protected $primaryKeyValue;

  /**
   * If true, then load() was called to populate the data in this object.
   * This will be used to determine if this is a new record or updating an
   * existing one.
   *
   * @var boolean
   */
  protected $newRecord;

  /**
   * Initilizes the Item object
   *
   * @param object
   */
  public function __construct($index = null)
  {
    parent::__construct();

    $this->primaryKeyValue = null;

    if ( $index !== null )
    {
      $this->setPriKeyValue($index);
    }

    $this->newRecord = true;
  }

  /**
   * Providing some easy access to info
   *
   * @param string Member variable requested
   * @return mixed
   */
  public function __get($var)
  {
    switch ( strtolower($var) )
    {
      case 'id':
        $rtn = $this->primaryKeyValue;
        break;

      case 'primarykey':
        $rtn = $this->source->primaryKey;
        break;

      default:
        $rtn = parent::__get($var);
        break;
    }

    return $rtn;
  }

  /**
   * Providing some easy access to setting information here
   *
   * @param string Member variable to set
   * @param mixed Value to assign
   */
  public function __set($var, $value)
  {
    // Direct an attempt to set the primary key by name to the ID instead.
    if ( $var == $this->source->primaryKey )
    {
      $var = 'id';
    }

    switch ($var)
    {
      case 'id':
        $this->setPriKeyValue($value);
        break;

      default:
        parent::__set($var, $value);
        break;
    }
  }

  /**
   * Determines if the requested object property exists.
   * Need this for template support.
   *
   * @param string Name of the propery
   * @return boolean
   */
  public function __isset($var)
  {
    if ( strtolower($var) == 'id' )
    {
      return true;
    }

    if ( strtolower($var) == 'primaryKey' )
    {
      return true;
    }

    return parent::__isset($var);
  }

  /**
   * Some pretty good default settings when cloning a Record.
   * Clear out the primary key, and mark this as a new record.
   *
   */
  public function __clone()
  {
    $this->primaryKeyValue = null;
    $this->newRecord       = true;
  }

  /**
   * Append Record specific debug info to the parent
   *
   * @return string
   */
  public function debug()
  {
    $rtn  = "Primary Key: ".$this->source->getPrimaryKeyName();
    $rtn .= " = ".$this->primaryKeyValue."\n";

    $rtn .= parent::debug();

    return $rtn;
  }

  /**
   * Override the parent to run any attempt to set a value through the
   * appropriate filtering for the field type.
   *
   * @param string Field name to set
   * @param mixed
   *
   * @return this
   */
  public function setValue($field, $value)
  {
    if ( $this->source->fields->exists($field) )
    {
      $this->dataItem[$field] = $this->source->fields->$field->setValue($value);
    }
    else
    {
      if ( $field == $this->source->getPrimaryKeyName() )
      {
        $this->setPriKeyValue($value);
      }
    }

    return $this;
  }

  /**
   * Used to provide a quicker way to access the possible values of an
   * enumerated list.  Will return an empty array if the field is not an
   * enum.
   *
   * @param string Name of the field
   *
   * @return List of allowable values, indexed by the values
   */
  public function getValues($fieldName)
  {
    $rtn = array();

    $field = $this->getSource()->getFieldSet()->getField($fieldName);

    if ( is_object($field) )
    {
      if ( $field instanceOf \Metrol\Db\Field\Enumerated )
      {
        $rtn = $field->getKeyedValues();
      }
    }

    return $rtn;
  }

  /**
   * Allow an override of the default new record setting.
   *
   * @param boolean
   *
   * @return this
   */
  public function setNewRecord($flag)
  {
    if ( $flag )
    {
      $this->newRecord = true;
    }
    else
    {
      $this->newRecord = false;
    }

    return $this;
  }

  /**
   * Sets the value of the primary key
   *
   * @param mixed
   *
   * @return this
   */
  public function setPriKeyValue($value)
  {
    $this->primaryKeyValue = $this->source->pktype->setValue($value);

    return $this;
  }

  /**
   * Loads the record into memory based on the primary key value.
   * A caller may optionally override the primary key field with a different
   * field that the value must match to.
   *
   * @param integer Primary key
   * @param string Field to compare the value against
   *
   * @return this
   */
  public function load($value = null, $field = null)
  {
    // Need either a passed in pk value, or one already stored.
    if ( $value === null and $this->primaryKeyValue === null )
    {
      return $this;
    }

    if ( $value === null )
    {
      $value = $this->primaryKeyValue;
    }

    $pkName = $this->getSource()->getPrimaryKeyName();

    if ( $field === null )
    {
      $field = $pkName;
    }

    parent::load($value, $field);

    if ( $this->loadFlag )
    {
      $this->newRecord  = false;
    }
    else
    {
      $this->newRecord       = true;
      $this->primaryKeyValue = null;

      if ( $field != $pkName )
      {
        $this->setValue($field, $value);
      }
    }

    return $this;
  }

  /**
   * Saves the data in this object back to the database.
   * For new records, the primary key value will be updated.
   *
   * @return this
   */
  public function save()
  {
    if ( $this->newRecord )
    {
      $pkVal = $this->source
                    ->driver->insert($this->source, $this->dataItem, $this->id);

      $this->primaryKeyValue = $pkVal;
    }
    else
    {
      $this->source
           ->driver->update($this->source, $this->dataItem, $this->id);
    }

    return $this;
  }

  /**
   * Deletes the record from the database
   *
   */
  public function delete()
  {
    $this->source->driver->delete($this->source, $this->id);

    $this->primaryKeyValue = null;
    $this->resetValues();
    $this->newRecord = true;
  }
}
