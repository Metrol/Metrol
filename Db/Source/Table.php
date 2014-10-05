<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source;

/**
 * Describes a Database table, what fields it has, and where that table is
 * located via its DSN
 *
 */
abstract class Table extends \Metrol\Db\Source
{
  /**
   * The primary key field for this table.  Yes, only going to support one
   * PK per table.
   *
   * @var \Metrol\Db\Field
   */
  protected $primaryKey;

  /**
   * Keeps all the field definitions for this data source readily available
   *
   * @var \Metrol\Db\Field\Set
   */
  protected $fields;

  /**
   * Initilizes the Table object
   *
   * @param \Metrol\Db\Driver
   * @param string Name of the table
   * @param string Name of the db schema the table is in
   */
  public function __construct(\Metrol\Db\Driver $driver, $tableName, $schema = null)
  {
    parent::__construct($driver, $tableName, $schema);

    $this->primaryKey = null;
    $this->fields     = new \Metrol\Db\Field\Set;
  }

  /**
   * Provide some readily accessible variables for callers needing read only
   * information.
   *
   * @param string Key value
   *
   * @return mixed
   */
  public function __get($var)
  {
    $var = strtolower($var);
    $rtn = null;

    switch ($var)
    {
      case 'fields': // The set of fields
        $rtn = $this->getFieldSet();
        break;

      case 'fqpk': // Full Qualified Primary Key field, quoted and aliased
        $rtn = $this->getFQPK();
        break;

      case 'pk':
        $rtn = $this->getPrimaryKeyName();
        break;

      case 'primarykey':
        $rtn = $this->getPrimaryKeyName();
        break;

      case 'pktype': // Provides the full primary key field type
        $rtn = $this->primaryKey;
        break;

      default:
        $rtn = parent::__get($var);
        break;
    }

    return $rtn;
  }

  /**
   * The set of data type definitions in here
   *
   * @return \Metrol\Db\Field\Set
   */
  public function getFieldSet()
  {
    return $this->fields;
  }

  /**
   * The data type definition for the specified field
   *
   * @param string
   *
   * @return \Metrol\Db\Field
   */
  public function getField($fieldName)
  {
    return $this->getFieldSet()->getField($fieldName);
  }

  /**
   * Provide the primary key's field name
   *
   * @return string
   */
  public function getPrimaryKeyName()
  {
    if ( $this->primaryKey === null )
    {
      return '';
    }

    return $this->primaryKey->getFieldName();
  }

  /**
   * Produce a Full Qualified Primary Key (FQPK) quoted, and with any alias
   * information that may need to be added to make it ready for going into
   * an SQL statement.
   *
   * @return string
   */
  public function getFQPK()
  {
    $rtn = $this->getPrimaryKeyName();

    if ( strlen($rtn) == 0 )
    {
      return '';
    }

    if ( strlen($this->alias) > 0 )
    {
      $rtn .= '"'.$this->alias.'".'.$rtn;
    }

    return $rtn;
  }

  /**
   * The primary key field type object
   *
   * @return \Metrol\Db\Field
   */
  public function getPrimaryKeyField()
  {
    return $this->primaryKey;
  }
}
