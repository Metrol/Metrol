<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Body;
use Metrol\HTML as html;

/**
 * The body of the HTML document.
 *
 */
class Area
{
  /**
   * The body tag that we're working with here
   *
   * @var \Metrol\HTML\Body
   */
  private $body;

  /**
   * A list of content stacks.  Each one will be displayed in the order it
   * is added to the list.
   *
   * @var array
   */
  protected $bodyStack;

  /**
   * When set, the closing body tag will not be produced.  This allows for
   * additional content to be added, passing the responsiblity of closure to
   * somewhere else.
   *
   * @var boolean
   */
  private $noClose;

  /**
   * Initialize this thing
   */
  public function __construct()
  {
    $this->body = new html\Body();
    $this->bodyStack = array();
    $this->noClose   = false;
  }

  /**
   * Produce the Body area
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = $this->buildArea();

    return $rtn;
  }

  /**
   * Used to specify whether or not to close the body tag
   *
   * @param boolean
   * @return this
   */
  public function setNoClose($flag)
  {
    if ( $flag )
    {
      $this->noClose = true;
    }
    else
    {
      $this->noClose = false;
    }

    return $this;
  }

  /**
   * Sets the content to appear in the body, replacing anything that was
   * previously here.
   *
   * @param string
   * @return this
   */
  public function setContent($content)
  {
    $this->clearContent();
    $this->addContent($content);

    return $this;
  }

  /**
   * Adds content to the stack of stuff to display
   *
   * @param string
   * @return this
   */
  public function addContent($content)
  {
    $this->bodyStack[] = $content;

    return $this;
  }

  /**
   * Clears out all the content from the body, all ready to start fresh!
   *
   * @return this
   */
  public function clearContent()
  {
    $this->bodyStack = array();

    return $this;
  }

  /**
   * Provide the body tag object
   *
   * @return \Metrol\HTML\Body
   */
  public function getBodyTag()
  {
    return $this->body;
  }

  /**
   * Assembles the body
   *
   * @return string
   */
  protected function buildArea()
  {
    $rtn = '';

    $this->body->setContent("\n");

    foreach ( $this->bodyStack as $content )
    {
      $this->body->addContent($content."\n");
    }

    if ( $this->noClose )
    {
      $this->body->setClosureType(\Metrol\HTML\Body::CLOSE_NONE);
    }

    $rtn = strval($this->body);

    $this->body->setClosureType(\Metrol\HTML\Body::CLOSE_CONTENT);

    return $rtn;
  }
}
