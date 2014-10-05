<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the paragraph tag
 *
 */
class Paragraph extends Tag
{
  /**
   * Instantiate the tag
   *
   */
  public function __construct()
  {
    parent::__construct('p', self::CLOSE_CONTENT);
  }
}
