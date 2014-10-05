<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * The break tag
 */
class Br extends Tag
{
  /**
   * Initialize the Break object
   */
  public function __construct()
  {
    parent::__construct('br', self::CLOSE_SELF);
  }
}