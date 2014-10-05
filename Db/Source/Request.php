<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source;

/**
 * Used by a DAO to assemble everything the Source Bank would need to know to
 * provide the correct data source.
 *
 */
class Request
{
  /**
   * Data source types that can be accessed
   *
   * @const
   */
  const SOURCE_TABLE    = 1;
  const SOURCE_VIEW     = 2;
  const SOURCE_FUNCTION = 3;

  /**
   * The Name of the data source
   *
   * @var string
   */
  public $name;

  /**
   * The DSN the data source resides within
   *
   * @var string
   */
  public $dsn;

  /**
   * Schema of the data source
   *
   * @var string
   */
  public $schema;

  /**
   * Alias that should be used for the data source in SQL statements
   *
   * @var string
   */
  public $alias;

  /**
   * The type of source looking to connect to
   *
   * @var integer
   */
  public $type;

  /**
   * For functions, this contains the list of arguments to be passed in
   *
   * @var array
   */
  public $args;

  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    $this->name   = '';
    $this->dsn    = '';
    $this->schema = null;
    $this->alias  = null;
    $this->args   = array();
    $this->type   = self::SOURCE_TABLE;
  }

  /**
   * Assembles parts of this object into a string that can be used by the data
   * bank to index the source it makes.
   *
   * @return string
   */
  public function getKey()
  {
    $key = $this->dsn.':';

    switch ( $this->type )
    {
      case self::SOURCE_TABLE:
        $key .= 'Table:';
        break;

      case self::SOURCE_VIEW:
        $key .= 'View:';
        break;

      case self::SOURCE_FUNCTION:
        $key .= 'Function:';
        break;

      default:
        break;
    }

    $key .= $this->name;

    if ( strlen($this->schema) > 0 )
    {
      $key .= ':'.$this->schema;
    }

    return $key;
  }

  /**
   * Diagnostic output of this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = 'Diagnostic output from '.get_class($this);

    foreach ( get_object_vars($this) as $key => $val )
    {
      $rtn .= $key.' = '.$val."\n";
    }

    return $rtn;
  }
}
