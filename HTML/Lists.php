<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Acts as a base class for the Ordered and Unordered lists
 */
abstract class Lists extends Tag
{
  /**
   * List of attached list items
   *
   * @var array
   */
  private $items;

  /**
   * Initialize the Lists object
   */
  public function __construct($tagName)
  {
    parent::__construct($tagName, self::CLOSE_CONTENT);
    $this->items = array();
  }

  /**
   * An override of the output so as to get all the list items displayed
   *
   * @return string
   */
  public function output()
  {
    $rtn = $this->open()."\n";

    foreach ( $this->items as $listItem )
    {
      $rtn .= $listItem."\n";
    }

    $rtn .= $this->close()."\n";

    return $rtn;
  }

  /**
   * Adds a new list item with the string passed in
   *
   * @param string
   * @return \Metrol\HTML\ListItem
   */
  public function addItem($content)
  {
    $li = new ListItem($content);

    $this->items[] = $li;

    return $li;
  }

  /**
   * Adds a sub-list to this list
   *
   * @param object
   */
  public function addList(Lists $list)
  {
    $this->items[] = $list;
  }
}