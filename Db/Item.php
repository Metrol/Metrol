<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db;

/**
 * Defines a read only record for a database view
 *
 */
abstract class Item extends \Metrol\Data\Item
{
  /**
   * Keep a copy of the Db Data Source this record is from
   *
   * @var \Metrol\Db\Source
   */
  protected $source;

  /**
   * The SQL Engine used by the load method
   *
   * @var \Metrol\Db\SQL
   */
  protected $sqlEngine;

  /**
   * Keeps track if a load attempt was successful or not
   *
   * @var boolean|null
   */
  protected $loadFlag;

  /**
   * Initilizes the Item object
   *
   * @param object
   */
  public function __construct()
  {
    parent::__construct();

    $this->source   = null;
    $this->loadFlag = null;

    $this->source = $this->initDataSource();

    $this->sqlEngine = $this->source->getSQLEngine();
    $this->sqlEngine->setLimit(1);
  }

  /**
   * Loads the record into memory based on the primary key value.
   * A caller may optionally override the primary key field with a different
   * field that the value must match to.
   *
   * @param mixed A value to lookup
   * @param string Field to compare the value against
   * @return this
   */
  public function load($value, $field)
  {
    if ( !is_numeric($value) )
    {
      $value = "'".$value."'";
    }

    $sql = $this->getLoadSQL()->where('obj."'.$field.'" = '.$value);

    $qr    = $this->source->driver->queryNoCache($sql);
    $dbObj = $this->source->driver->fetch($qr, Driver::FETCH_OBJECT);

    if ( $dbObj === false )
    {
      $this->resetValues(); // Clear out any other stored values
      $this->loadFlag = false;

      return $this;
    }
    else
    {
      $this->loadFlag = true;
    }

    $fields = get_object_vars($dbObj);

    foreach ( $fields as $key => $val )
    {
      $this->setValue($key, $val);
    }

    return $this;
  }

  /**
   * Used for 1->1 relationships, will load a record for this object based on
   * the primary key of the passed in object.
   *
   * @param  \Metrol\Db\Item\Record
   * @param  string Optionally specify the field in this object to match to
   * @return this
   */
  public function loadFromRecord(Item\Record $recordObj, $pkField = null)
  {
    if ( $pkField === null )
    {
      $field = $recordObj->primaryKey;
    }
    else
    {
      $field = $pkField;
    }

    $this->load($recordObj->id, $field);

    return $this;
  }

  /**
   * Produce the SQL object used to load a record
   *
   * @return \Metrol\Db\SQL
   */
  public function getLoadSQL()
  {
    return $this->sqlEngine;
  }

  /**
   * Provide the Db Source for this object
   *
   * @return \Metrol\Db\Source
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Assigns a table to the Table member variable
   *
   * @return \Metrol\Db\Source
   */
  abstract protected function initDataSource();
}
