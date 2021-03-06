<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Menu;

/**
 * Keeps a set of Menu Items for the Menu object
 *
 */
class Set extends \Metrol\Data\Set
{
  /**
   * Instantiate the menu item set
   *
   */
  public function __construct()
  {
    parent::__construct();

    $this->itemType = "\Metrol\HTML\Menu\Item";
  }
}
