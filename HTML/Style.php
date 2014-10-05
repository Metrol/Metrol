<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the style tag
 */
class Style extends Tag
{
  /**
   * Instantiates the tag
   *
   * @param string
   */
  public function __construct()
  {
    parent::__construct('style', self::CLOSE_CONTENT);

    $this->setMedia('screen');
    $this->setType('text/css');
  }

  /**
   * Sets the "media" attribute for the tag
   *
   * @param string The media type this link is meant for
   * @return this
   */
  public function setMedia($val)
  {
    $this->attribute()->media = $val;

    return $this;
  }

  /**
   * Sets the "type" attribute for the tag
   *
   * @param string
   * @return \Metrol\HTML\Link
   */
  public function setType($val)
  {
    $this->attribute()->type = $val;

    return $this;
  }
}
