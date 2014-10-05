<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source;

/**
 * Keeps a cache of database tables, view, and function definitions that DAO's
 * can request from here so the structure only needs to be looked up once.
 *
 */
class Manage
{
  /**
   * The single instance of this class allowed.
   *
   * @var this
   */
  static private $thisObj;

  /**
   * List of database tables index by their DSN:Schema:Name
   *
   * @var array
   */
  private $sources;

  /**
   * Initialize the Manage object
   *
   */
  private function __construct()
  {
    $this->sources = array();
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
   * Provides the data source based on the request object specified.
   *
   * @param \Metrol\Db\Source\Request
   *
   * @return \Metrol\Db\Source
   */
  static public function getSource(Request $req)
  {
    $o = self::getInstance();
    $key = $req->getKey();

    if ( array_key_exists($key, $o->sources) )
    {
      return $o->sources[$key];
    }

    $source = null;

    switch ( $req->type )
    {
      case Request::SOURCE_TABLE:
        $source = $o->initTable($req);
        break;

      case Request::SOURCE_VIEW:
        $source = $o->initView($req);
        break;

      case Request::SOURCE_FUNCTION:
        $source = $o->initFunc($req);
        break;

      default:
        break;
    }

    if ( $source !== null )
    {
      $o->sources[$key] = $source;
    }

    return $source;
  }

  /**
   * Look to the Source Manager to locate a Table
   *
   * @param string $dsn       Data Source Name
   * @param string $tableName Table, View or Function Name
   * @param string $schema    Schema/DB Namespace Name
   *
   * @return \Metrol\Db\Source
   */
  static public function getTable($dsn, $tableName, $schema = null)
  {
    $req = new Request;
    $req->dsn    = $dsn;
    $req->name   = $tableName;
    $req->schema = $schema;
    $req->type   = Request::SOURCE_TABLE;

    return self::getSource($req);
  }

  /**
   * Look to the Manager to locate a View
   *
   * @param string $dsn      Data Source Name
   * @param string $viewName View Name
   * @param string $schema   Schema/DB Namespace Name
   *
   * @return \Metrol\Db\Source
   */
  static public function getView($dsn, $viewName, $schema = null)
  {
    $req = new Request;
    $req->dsn    = $dsn;
    $req->name   = $viewName;
    $req->schema = $schema;
    $req->type   = Request::SOURCE_VIEW;

    return self::getSource($req);
  }

  /**
   * Look to the Manager to locate a Function
   *
   * @param string $dsn          Data Source Name
   * @param string $functionName Function Name
   * @param string $schema       Schema/DB Namespace Name
   *
   * @return \Metrol\Db\Source
   */
  static public function getFunc($dsn, $functionName, $schema = null)
  {
    $req = new Request;
    $req->dsn    = $dsn;
    $req->name   = $functionName;
    $req->schema = $schema;
    $req->type   = Request::SOURCE_FUNCTION;

    return self::getSource($req);
  }

  /**
   * Get the correct table object together
   *
   * @param \Metrol\Db\Source\Request
   *
   * @return \Metrol\Db\Source\Table
   */
  private function initTable(Request $req)
  {
    $rtn = null;

    $driver = \Metrol\Db\Driver\Fetch::getDriver($req->dsn);

    if ( $driver instanceOf \Metrol\Db\Driver\PostgreSQL )
    {
      $rtn = new Table\PostgreSQL($driver, $req->name, $req->schema);
    }

    return $rtn;
  }

  /**
   * Get the correct view object together
   *
   * @param \Metrol\Db\Source\Request
   *
   * @return \Metrol\Db\Source\View
   */
  private function initView(Request $req)
  {
    $rtn = null;

    $driver = \Metrol\Db\Driver\Fetch::getDriver($req->dsn);

    if ( $driver instanceOf \Metrol\Db\Driver\PostgreSQL )
    {
      $rtn = new View\PostgreSQL($driver, $req->name, $req->schema);
    }

    return $rtn;
  }

  /**
   * Get the correct function object together
   *
   * @param \Metrol\Db\Source\Request
   *
   * @return \Metrol\Db\Source\Func
   */
  private function initFunc(Request $req)
  {
    $rtn = null;

    $driver = \Metrol\Db\Driver\Fetch::getDriver($req->dsn);

    if ( $driver instanceOf \Metrol\Db\Driver\PostgreSQL )
    {
      $rtn = new Func\PostgreSQL($driver, $req->name, $req->schema, $req->args);
    }

    return $rtn;
  }
}
