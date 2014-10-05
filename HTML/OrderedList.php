<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Describe UnorderedList
 */
class OrderedList extends Lists
{
  /**
   * Initialize the UnorderedList object
   */
  public function __construct()
  {
    parent::__construct('ol');
  }
}