<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source\View;

/**
 * A PostgreSQL table definition.
 */
class PostgreSQL extends \Metrol\Db\Source\View
{
  /**
   * Initialize the PostgreSQL object
   *
   * @param object
   */
  public function __construct(\Metrol\Db\Driver\PostgreSQL $driver,
                               $tableName, $schema = null)
  {
    parent::__construct($driver, $tableName, $schema);

    if ( $schema === null )
    {
      $this->schema = 'public';
    }
    else
    {
      $this->schema = $schema;
    }
  }

  /**
   * Provide an instance of the SQL Engine that supports this table's server
   *
   * @return \Metrol\Db\SQL
   */
  public function getSQLEngine()
  {
    $sqlEng = new \Metrol\Db\SQL\PostgreSQL($this);

    return $sqlEng;
  }
}
