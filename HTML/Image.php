<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the img tag
 */
class Image extends Tag
{
  /**
   * Keeps track of the allowed image types
   *
   * @var array
   */
  private static $imageTypes;

  /**
   * The kinds of horizontal alignment options
   *
   * @var array
   */
  private static $horizAlignRef;

  /**
   * The kinds of vertical alignment options
   *
   * @var array
   */
  private static $vertAlignRef;

  /**
   * The URL object that will be put into the "src=" attribute
   *
   * @var \Metrol\URL
   */
  private $sourceURL;

  /**
   * @param string
   */
  public function __construct($fileName)
  {
    parent::__construct('img', self::CLOSE_SELF);

    self::initRefVars();

    $this->sourceURL = new \Metrol\URL();
    $this->setImage($fileName);
  }

  /**
   * Adds a few extra attributes on the way out the door.
   *
   * @return string
   */
  public function __toString()
  {
    $srcURL = strval( $this->sourceURL );

    if ( strlen($srcURL) > 0 )
    {
      $this->attribute()->src = $srcURL;
    }

    return parent::__toString();
  }

  /**
   * Sets the image source URL
   *
   * @param string
   * @return \Metrol\HTML\Image
   */
  public function setImage($fileName)
  {
    $this->sourceURL->setURL($fileName);

    return $this;
  }

  /**
   * Sets the alternate text for the image
   *
   * @param string
   * @return \Metrol\HTML\Image
   */
  public function setAlt($text)
  {
    $this->attribute()->alt = $text;

    return $this;
  }

  /**
   * Specifies the border attribute
   *
   * @param integer
   * @return \Metrol\HTML\Image
   */
  public function setBorder($size)
  {
    $this->attribute()->border = intval($size);

    return $this;
  }

  /**
   * Specifies the alignment of an image according to surrounding elements
   *
   * @param string
   * @return \Metrol\HTML\Image
   */
  public function setAlign($alignment)
  {
    $align = strtolower($alignment);

    if ( !in_array($align, self::$horizAlignRef) )
    {
      return $this;
    }

    $this->attribute()->align = $align;

    return $this;
  }

  /**
   * Specifies the vertical alignment of the image
   *
   * @param string
   * @return \Metrol\HTML\Image
   */
  public function setVerticalAlign($alignment)
  {
    $align = strtolower($alignment);

    if ( strpos($alignment, "%") or in_array($align, self::$vertAlignRef) )
    {
      $this->addStyle("vertical-align", $align);
    }

    return $this;
  }

  /**
   * Provide the source URL object attached to this tag.
   *
   * @return \Metrol\URL
   */
  public function getImageURL()
  {
    return $this->sourceURL;
  }

  /**
   * Initializes the static reference vars if they haven't had values stuffed
   * in them yet.
   */
  private static function initRefVars()
  {
    if ( count(self::$imageTypes) > 0 )
    {
      return;
    }

    self::$imageTypes = array('png', 'jpg', 'php', 'gif', 'jpeg');

    self::$horizAlignRef = array('top', 'bottom', 'middle', 'left', 'right');

    self::$vertAlignRef = array('baseline', 'sub', 'super', 'top', 'text-top',
                                'middle', 'bottom', 'text-bottom', 'length');
  }
}
