<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the span tag
 */
class Span extends Tag
{
  /**
   * Pass in the text that will show within the span
   *
   * @param string
   */
  public function __construct($text = '')
  {
    parent::__construct('span', self::CLOSE_CONTENT);

    $this->setContent($text);
  }
}
