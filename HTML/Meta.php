<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the meta tag
 */
class Meta extends Tag
{
  /**
   * Takes no arguments as these types of tags can vary to wildly
   */
  public function __construct()
  {
    parent::__construct('meta', self::CLOSE_SELF);
  }

  /**
   * Sets the "content" attribute for the tag
   *
   * @param string
   * @return \Metrol\HTML\Meta
   */
  public function setContent($val)
  {
    $this->attribute()->set('content', $val);

    return $this;
  }

  /**
   * Used to define the character encoding for a page
   *
   * @param string Character Set
   * @return \Metrol\HTML\Meta
   */
  public function setCharacterEncoding($charSet)
  {
    $this->attribute()->set('http-equiv', 'Content-Type');
    $this->attribute()->set('content', 'text/html; charset='.$charSet);

    return $this;
  }

  /**
   * Same as setCharacterEncoding, but in an HTML5 compliant way
   *
   * @param string Character Set
   * @return this
   */
  public function setCharSet($charSet)
  {
    $this->attribute()->set('charset', $charSet);

    return $this;
  }

  /**
   * Take an array list and puts together the keyword meta tag
   *
   * @var array
   * @return \Metrol\HTML\Meta
   */
  public function setKeywords(array &$keyWords)
  {
    if ( count($keyWords) > 0 ) {
      $keyStr = \Metrol\Text::arrayToStr($keyWords);

      $this->setName('keywords');
      $this->setContent($keyStr);
    }

    return $this;
  }

  /**
   * A tag that flags Internet Explorer to go into Version 7 compatibility
   * mode.
   */
  public function ie7Compat()
  {
    $this->attribute()->set('http-equiv', 'X-UA-Compatible');
    $this->setContent('IE=EmulateIE7');

    return $this;
  }

  /**
   * Sets the Author of the site into the Meta tag.
   *
   * @param string
   * @return \Metrol\HTML\Meta
   */
  public function setAuthor($author)
  {
    $this->setName('author');
    $this->setContent($author);
  }

  /**
   * Sets the Copyright of the site into the Meta tag.
   *
   * @param string
   * @return \Metrol\HTML\Meta
   */
  public function setCopyright($copyRtDt)
  {
    $this->setName('copyright');
    $this->setContent($copyRtDt);
  }

  /**
   * Specifies whether or not search engine robots should index this page.
   *
   * @param boolean
   * @return \Metrol\HTML\Meta
   */
  public function setRobotIndex($flag)
  {
    $this->setName('robots');

    if ( $flag )
    {
      $this->setContent('index');
    }
    else
    {
      $this->setContent('noindex');
    }

    return $this;
  }

  /**
   * Used to set in a refresh to the Meta tag
   *
   * @param integer Delay in seconds
   * @param string URL to refresh
   * @return \Metrol\HTML\Meta
   */
  public function setRefresh($seconds, $url = '')
  {
    $seconds = intval($seconds);

    $this->attribute->set('http-equiv', 'refresh');

    if ( strlen($url) > 0 )
    {
      $this->setContent($seconds."; url=".$url);
    }

    return $this;
  }
}
