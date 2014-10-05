<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source;

/**
 * Describes a Database Function Call
 */
abstract class Func extends \Metrol\Db\Source
{
  /**
   * Maintain the list of arguments to pass into the function
   *
   * @var array
   */
  protected $args;

  /**
   * Initilizes the Db Function Source
   *
   * @param \Metrol\Db\Driver
   * @param string Name of the function
   * @param string Name of the db schema the function is in
   */
  public function __construct(\Metrol\Db\Driver $driver, $funcName,
                              $schema = null)
  {
    parent::__construct($driver, $funcName, $schema);

    $this->args = array();
  }

  /**
   * Assemble the FQTN to be used in an SQL statement, complete with arguments
   * in the function call
   *
   * @return string
   * @TODO Need to add string support for arguments
   */
  public function getFQTN()
  {
    $rtn = '';

    if ( $this->schema !== null )
    {
      $rtn .= '"'.$this->getSchema().'".';
    }

    $rtn .= '"'.$this->getName().'"';

    $rtn .= '('.implode(', ', $this->args).')';

    if ( strlen($this->getAlias) > 0 )
    {
      $rtn .= ' AS "'.$this->getAlias().'"';
    }

    return $rtn;
  }

  /**
   * Set the arguments to be passed into the function
   *
   * @param array
   */
  public function setArgs(array $args)
  {
    $this->args = $args;
  }
}
