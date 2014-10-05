<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Used to construct a menu with UL and LI tags that most JavaScript and CSS
 * utilities can make fancy drop down menus with.
 *
 */
class Menu
{
  /**
   * The list of items that make up this menu
   *
   * @var \Metrol\HTML\Menu\Set
   */
  protected $mSet;

  /**
   */
  public function __construct()
  {
    $this->initMenuSet();
  }

  /**
   * Initialize the Menu Set object items will be getting stored in here
   *
   */
  protected function initMenuSet()
  {
    $this->mSet = new Menu\Set;
  }
}
