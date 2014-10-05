<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db;

/**
 * Describes a Database data source, what fields it has, and where it is
 * located via its DSN
 *
 */
abstract class Source
{
  /**
   * The database driver needed for accessing this data source
   *
   * @var Metrol\Db\Driver
   */
  protected $driver;

  /**
   * Name of the data source
   *
   * @var string
   */
  protected $name;

  /**
   * The schema name this data source falls within
   *
   * @var string
   */
  protected $schema;

  /**
   * Alias name to use in SQL statements for FROM and JOIN.
   *
   * @var string
   */
  protected $alias;

  /**
   * Initilizes the Db Source object
   *
   * @param \Metrol\Db\Driver
   * @param string Name of the table, view, or function
   */
  public function __construct(Driver $driver, $dataSource, $schema = null)
  {
    $this->driver     = $driver;
    $this->name       = $dataSource;
    $this->schema     = $schema;
  }

  /**
   * Provide the Fully Qualified Table Name (FQTN) as the default output of
   * this class.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getFQTN();
  }

  /**
   * Provide some readily accessible variables for callers needing read only
   * information.
   *
   * @param string Key value
   * @return mixed
   */
  public function __get($var)
  {
    $var = strtolower($var);
    $rtn = null;

    switch ($var)
    {
      case 'driver':
        $rtn = $this->getDriver();
        break;

      case 'schema':
        $rtn = $this->getSchema();
        break;

      case 'name':
        $rtn = $this->getName();
        break;

      case 'alias':
        $rtn = $this->getAlias();
        break;

      case 'fqtn': // Fully Qualified Table Name default
        $rtn = $this->getFQTN();
        break;

      default:
        $rtn = null;
        break;
    }

    return $rtn;
  }

  /**
   * Just the name of the data source, without quotes or schema info
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * The schema this data source is attached to
   *
   * @return string
   */
  public function getSchema()
  {
    return $this->schema;
  }

  /**
   * Assemble a Fully Qualified Table Name properly quoted and schema added.
   *
   * @return string
   */
  public function getFQTN()
  {
    $rtn = '';

    if ( $this->schema !== null )
    {
      $rtn .= '"'.$this->getSchema().'".';
    }

    $rtn .= '"'.$this->getName().'"';

    if ( strlen($this->getAlias) > 0 )
    {
      $rtn .= ' AS "'.$this->getAlias().'"';
    }

    return $rtn;
  }

  /**
   * Sets the alias to use for this table in SQL statements
   *
   * @param string
   * @return this
   */
  public function setAlias($alias)
  {
    $this->alias = strval($alias);

    return $this;
  }

  /**
   * The alias that was set, if anything
   *
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
  }

  /**
   * The DB Driver that supports this data source
   *
   * @return \Metrol\Db\Driver
   */
  public function getDriver()
  {
    return $this->driver;
  }

  /**
   * Provide the Data Source Name (DSN) that the driver was defined by
   *
   * @return string
   */
  public function getDSN()
  {
    return $this->driver->getDSN();
  }

  /**
   * An instance of the SQL Engine that supports this table's server
   *
   * @return \Metrol\Db\SQL
   */
  abstract public function getSQLEngine();
}
