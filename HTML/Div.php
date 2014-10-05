<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the div tag
 */
class Div extends Tag
{
  /**
   */
  public function __construct()
  {
    parent::__construct('div', self::CLOSE_CONTENT);
  }

  /**
   * Sets the kind of overflow the div area should respect
   *
   * @param string
   * @return this
   */
  public function setOverflow($ovValue)
  {
    $allowed = array('visible', 'hidden', 'scroll', 'auto', 'inherit');
    $ovValue = strtolower(substr(trim($ovValue), 0, 7));

    if ( in_array($ovValue, $allowed) )
    {
      $this->addStyle('overflow', $ovValue);
    }

    return $this;
  }
}
