<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the link tag
 */
class Link extends Tag
{
  /**
   * Takes no arguments as these types of tags can vary to wildly
   */
  public function __construct()
  {
    parent::__construct('link', self::CLOSE_SELF);
  }

  /**
   * Sets the "rel" attribute for the tag
   *
   * @param string
   * @return this
   */
  public function setRel($val)
  {
    $val = strtolower($val);

    $relTypes = array(
      "alternate", "archives", "author", "bookmark", "external", "first",
      "help", "icon", "last", "license", "next", "nofollow", "noreferrer",
      "pingback", "prefetch", "prev", "search", "sidebar", "stylesheet",
      "tag", "up");

    if ( in_array($val, $relTypes) )
    {
      $this->attribute()->rel = $val;
    }

    return $this;
  }

  /**
   * Sets the "href" attribute for the tag
   *
   * @param string
   * @return this
   */
  public function setHref($val)
  {
    $this->attribute()->href = $val;

    return $this;
  }

  /**
   * Sets the "type" attribute for the tag
   *
   * @param string Specifies the MIME type of the linked document
   * @return this
   */
  public function setType($val)
  {
    $this->attribute()->type = $val;

    return $this;
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
}
