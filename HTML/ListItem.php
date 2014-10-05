<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Describe ListItem
 */
class ListItem extends Tag
{
  /**
   * Initialize the ListItem object
   */
  public function __construct($content = '')
  {
    parent::__construct('li', self::CLOSE_CONTENT);

    if ( strlen($content) > 0 )
    {
      $this->setContent($content);
    }
  }
}