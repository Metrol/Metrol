<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Item;

/**
 * A read only database record.
 *
 */
abstract class View extends \Metrol\Db\Item
{
  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    parent::__construct();
  }
}
