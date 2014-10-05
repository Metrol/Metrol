<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source;

/**
 * Describes a Database View
 */
abstract class View extends \Metrol\Db\Source
{
  /**
   * Initilizes the Db View object
   *
   * @param \Metrol\Db\Driver
   * @param string Name of the view
   * @param string Name of the db schema the view is in
   */
  public function __construct(\Metrol\Db\Driver $driver, $tableName,
                              $schema = null)
  {
    parent::__construct($driver, $tableName, $schema);
  }
}
