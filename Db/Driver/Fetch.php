<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Driver;

/**
 * Handles providing database drivers connected to servers by DSN name
 * 
 */
class Fetch
{
  /**
   * Singleton instance of this object
   *
   * @var this
   */
  static private $thisObj;

  /**
   * List of drivers indexed by their DSN names
   *
   * @var array
   */
  private $drivers;

  /**
   * Initialize the Connect object
   *
   * @param object
   */
  private function __construct()
  {
    $this->drivers = array();
  }

  /**
   * Provides an instance of this class
   *
   * @return this
   */
  static public function getInstance()
  {
    if ( !is_object(self::$thisObj) )
    {
      $thisClass = __CLASS__;
      self::$thisObj = new $thisClass;
    }

    return self::$thisObj;
  }

  /**
   * Provides the database driver requested from the DSN name.
   *
   * @param string Data Source Name (DSN)
   *
   * @return \Metrol\Db\Driver\
   */
  static public function getDriver($dsn)
  {
    $o = self::getInstance();

    if ( array_key_exists($dsn, $o->drivers) )
    {
      return $o->drivers[$dsn];
    }

    $driver = $o->selectDriver($dsn);

    return $driver;
  }

  /**
   * Selects the correct driver based on the type found in the DSN information
   *
   * @param array DSN information
   * @return \Metrol\Db\Driver
   */
  protected function selectDriver($dsn)
  {
    $driver  = null;
    $dsnFind = new \Metrol\Db\DSN\Find($dsn);
    $dsnInfo = $dsnFind->search();

    if ( $dsnInfo == null )
    {
      return null;
    }

    $dbType = strtolower($dsnInfo->type);

    switch ($dbType)
    {
      case 'postgresql':
        $driver = new \Metrol\Db\Driver\PostgreSQL;
        $driver->connectDSN($dsn, $dsnInfo);
        break;

      default:
        break;
    }

    return $driver;
  }
}
