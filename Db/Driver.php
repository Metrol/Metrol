<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db;

/**
 * Defines the basic set of methods required to be a Db Driver
 *
 */
interface Driver
{
  const FETCH_OBJECT = 0;
  const FETCH_ASSOC  = 1;
  const FETCH_ROW    = 2;

  /**
   * Establishes a connection by utilizing the provided DSN information
   *
   * @param array
   */
  public function connect(\Metrol\Db\DSN $dsn);

  /**
   * Shuts down the connection to the database server
   *
   */
  public function close();

  /**
   * Sends a query directly to the database.
   * The query resourse is cached internally for use with fetching and other
   * functions
   *
   * @param string $sql
   *
   * @return query resource
   */
  public function query($sql);

  /**
   * Sends a query directly to the database.
   * The query resourse is NOT cached internally.  Useful for embedded calls.
   *
   * @param string $sql
   *
   * @return query resource
   */
  public function queryNoCache($sql);

  /**
   * Specify what type of fetched object should be returned from a Fetch
   * operation.
   *
   * @param integer
   */
  public function setFetchType($fetchType);

  /**
   * Fetches a single row from the database.
   *
   * @param query resource
   * @param integer type of fetch to pull
   *
   * @return mixed
   */
  public function fetch($qr = null, $fetchType = null);

  /**
   * Runs the input string through the server specific text cleaning routine.
   *
   * @param string
   *
   * @return string
   */
  public function escapeString($text);
}
