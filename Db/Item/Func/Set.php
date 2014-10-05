<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Item\Func;

/**
 * A set of items returned from a database procedure/function
 *
 */
class Set extends \Metrol\Db\Item\Set
{
  /**
   * Initialize the Set object
   *
   * @param \Metrol\Db\Item\Func
   */
  public function __construct(\Metrol\Db\Item\Func $obj)
  {
    parent::__construct($obj);
  }
}
