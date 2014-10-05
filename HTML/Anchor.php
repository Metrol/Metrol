<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * An HTML Anchor tag.
 */
class Anchor extends Tag
{
  /**
   * Pass in the URL that will act as the base of this object.
   *
   * @param string URL to set the HREF to
   * @param string The text to link
   */
  public function __construct($url = '', $text = '')
  {
    parent::__construct('a', self::CLOSE_CONTENT);

    $this->linkText($text);

    if ( strtolower($url) == 'print' )
    {
      $this->printPage();
    }
    else
    {
      $this->setURL($url);
    }
  }

  /**
   * Sets the anchor URL and Title from the information derived from the
   * specified route.
   *
   * @param string $routeName Name of the route stored in the Cache
   * @param array $args Arguments to fill in the route with
   *
   * @return this
   */
  public function setRoute($routeName, array $args = null)
  {
    $route = \Metrol\Frame\HTTP\Route\Cache::getInstance()
             ->getRoute($routeName)
             ->clearArguments();

    if ( $args !== null )
    {
      foreach ( $args as $arg )
      {
        $route->addArguments($arg);
      }
    }

    $tagTitle = $route->getTagTitle();

    if ( strlen($tagTitle) > 0 )
    {
      $this->setTitle($tagTitle);
    }

    return $this;
  }

  /**
   * Assembles the URL and calls to the parent Tag class to put everything
   * together for an output.
   *
   * @return string
   */
  public function output()
  {
    $url = (string) $this->url();

    if ( strlen($url) > 0 )
    {
      $this->attribute()->href = $url;
    }

    $tag = parent::output();

    return $tag;
  }

  /**
   * Adds a new parameter to the URL
   *
   * @param string
   * @param string
   * @return this
   */
  public function param($key, $value)
  {
    $this->url()->param($key, $value);

    return $this;
  }

  /**
   * Sets the URL as a Javascript function
   *
   * @param string Javascript to run
   * @return this
   */
  public function setJS($javascriptCall)
  {
    $this->url()->setURL('javascript:'.$javascriptCall);

    return $this;
  }

  /**
   * Takes in what should be (not checked) a valid Email address and
   * creates the proper URL and linkText for it.
   *
   * @param string Email address to send to
   * @return this
   */
  public function setEmail($email)
  {
    $this->url()->setURL('mailto:'.$email);
    $this->linkText($email, TRUE);
    $this->setTitle('Send an Email to '.$email);

    return $this;
  }

  /**
   * Sets the text to be surrounded by this anchor tag.
   *
   * @param string
   * @return this
   */
  public function linkText($text, $cleanText = FALSE, $nowrap = FALSE)
  {
    if ( $cleanText )
    {
      $text = \Metrol\Text::htmlent($text);
    }

    if ( $nowrap )
    {
      $text = str_replace(' ', '&nbsp;', $text);
    }

    $this->setContent($text);

    return $this;
  }

  /**
   * Sets an image to be linked to.
   * If link text has been set that will become the title attribute for
   * the image.
   *
   * @param Metrol\HTML\Image
   * @return this
   */
  public function image(Image $image)
  {
    $img = clone $image;

    // When passing an image on in going to try to set it's title attribute by
    // either using the title from this tag, or the existing content text.
    if ( $this->attribute()->exists('title') )
    {
      $img->title($this->attribute->title);
    }
    elseif ( strlen($this->getContent()) > 0 )
    {
      $img->title($this->getContent());
    }

    // Always toss in a 0 border for a linked image.
    $img->border(0);

    $this->setContent($img);

    return $this;
  }

  /**
   * Puts all the attributes in place for a JavaScript print page call
   *
   * @return this
   */
  public function printPage()
  {
    $this->setJS('window.print()');
    $this->setTitle('Print the contents of this page');
    $this->setClass('linkButton');

    if ( strlen($this->getContent()) == 0 )
    {
      $this->setContent('Print Page');
    }

    return $this;
  }

  /**
   * Sets the local anchor name to go to in the URL.
   * In other words, adds the specified name as #name.
   *
   * @param string
   * @return this
   */
  public function anchorUrl($anchorName)
  {
    $this->url()->setAnchor($anchorName);

    return $this;
  }

  /**
   * Set the target to a new window
   *
   * @return this
   */
  public function newWindow()
  {
    $this->setTarget('_blank');

    return $this;
  }

  /**
   * Set the target to within the existing frame
   *
   * @return this
   */
  public function sameFrame()
  {
    $this->setTarget('_self');

    return $this;
  }

  /**
   * Set the target to the top most window that is open
   *
   * @return this
   */
  public function topWindow()
  {
    $this->setTarget('_top');

    return $this;
  }

  /**
   * Set the target to the immediate parent's frame or window
   *
   * @return this
   */
  public function parentFrame()
  {
    $this->setTarget('_parent');

    return $this;
  }

  /**
   * Actually sets the target
   *
   */
  private function setTarget($targetName)
  {
    $this->attribute->set('target', $targetName);
  }
}
