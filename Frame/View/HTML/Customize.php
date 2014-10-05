<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTML;

/**
 * Provides a structured means for specifying customer specific changes to the
 * look and feel of the site.
 *
 */
class Customize
{
  /**
   * Hold on to the Head Area object that will get passed to the view
   *
   * @var \Metrol\HTML\Head\Area
   */
  protected $headArea;

  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    $this->headArea = new \Metrol\HTML\Head\Area;
  }

  /**
   * Provide the header area for the page
   *
   * @return \Metrol\HTML\Head\Area
   */
  public function getHeadArea()
  {
    return $this->headArea;
  }
}
