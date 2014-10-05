<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Item\View;

/**
 * A set of View items
 */
class Set extends \Metrol\Db\Item\Set
{
  /**
   * Initialize the Set object
   *
   * @param \Metrol\Db\Item\View
   */
  public function __construct(\Metrol\Db\Item\View $obj)
  {
    parent::__construct($obj);
  }
}
