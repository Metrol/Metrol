<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the title tag
 */
class Title extends Tag
{
  /**
   * Pass in the text that will show in the Title area
   *
   * @param string
   */
  public function __construct($text = '')
  {
    parent::__construct('title', self::CLOSE_CONTENT);

    $this->setContent($text);
  }
}
