<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the Head tag
 */
class Head extends Tag
{
  /**
   */
  public function __construct()
  {
    parent::__construct('head', self::CLOSE_NONE);
  }
}
