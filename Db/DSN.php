<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db;

/**
 * Defines a Database Resource Name and all of its attributes
 *
 */
class DSN
{
  /**
   * The name this DSN resource will be referred to as
   *
   * @var string
   */
  public $resourceName;

  /**
   * Type of resource to connect to.
   *
   * @var string
   */
  public $type;

  /**
   * The name of the data resource, such as a database name
   *
   * @var string
   */
  public $name;

  /**
   * Schema name of this connection
   *
   * @var string
   */
  public $schema;

  /**
   * User name to connect to the data resource with
   *
   * @var string
   */
  public $user;

  /**
   * The password to connect with
   *
   * @var string
   */
  public $pass;

  /**
   * Host name or IP address to connect to
   *
   * @var string
   */
  public $host;

  /**
   * Port number of the TCP connection
   *
   * @var integer
   */
  public $port;

  /**
   * Instantiate the object
   *
   * @param string $resourceName The name of this DSN
   */
  public function __construct($resourceName)
  {
    $this->resourceName = $resourceName;

    // Default everything else to null
    $this->type     = null;
    $this->name     = null;
    $this->schema   = null;
    $this->user     = null;
    $this->pass     = null;
    $this->host     = null;
    $this->port     = null;
  }

  /**
   * Diagnostic output of this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = array();

    $rtn[] = 'Diagnostic output of '.get_class($this);
    $rtn[] = '------------------------------------------------------';
    $rtn[] = 'name   = '.$this->name;
    $rtn[] = 'type   = '.$this->type;
    $rtn[] = 'schema = '.$this->schema;
    $rtn[] = 'user   = '.$this->user;
    $rtn[] = 'pass   = '.$this->pass;
    $rtn[] = 'host   = '.$this->host;
    $rtn[] = 'port   = '.$this->port;

    return implode("\n", $rtn);
  }
}
