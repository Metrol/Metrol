<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define an entire HTML Page
 *
 */
class Page
{
  /**
   * The header area of the page
   *
   * @var \Metrol\HTML\Head\Area
   */
  protected $head;

  /**
   * The body of the document
   *
   * @var \Metrol\HTML\Body\Area
   */
  protected $body;

  /**
   * The footer area of the document
   *
   * @var \Metrol\HTML\Foot\Area
   */
  protected $foot;

  /**
   * Initilizes the Page object
   *
   * @param object
   */
  public function __construct()
  {
    $this->head = new Head\Area;
    $this->body = new Body\Area;
    $this->foot = new Foot\Area;
  }

  /**
   * Display the entirety of the page
   *
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * When set, the footer area will provide the closing body tag.  The body
   * area will not close itself.  Set to false, and the body will close itself.
   *
   * @param boolean
   * @return this
   */
  public function setFootClosesBody($flag)
  {
    $this->body->setNoClose($flag);
    $this->foot->setBodyClose($flag);

    return $this;
  }

  /**
   * Output the 3 areas of the HTML document
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    $rtn .= $this->head;
    $rtn .= $this->body;
    $rtn .= $this->foot;

    return $rtn;
  }

  /**
   * Provide the header area of the page
   *
   * @return \Metrol\HTML\Head\Area
   */
  public function getHeadArea()
  {
    return $this->head;
  }

  /**
   * Provide the body area of the page
   *
   * @return \Metrol\HTML\Body\Area
   */
  public function getBodyArea()
  {
    return $this->body;
  }

  /**
   * Provide the foot area of the page
   *
   * @return \Metrol\HTML\Foot\Area
   */
  public function getFootArea()
  {
    return $this->foot;
  }
}
