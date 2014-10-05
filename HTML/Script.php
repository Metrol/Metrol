<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the script tag
 */
class Script extends Tag
{
  /**
   * Can take in what kind of scripting language is being called.
   *
   * @param string
   */
  public function __construct()
  {
    parent::__construct('script', self::CLOSE_CONTENT);

    $this->setLanguage('JavaScript');
    $this->setType('text/javascript');
    $this->setContent(' ');
  }

  /**
   * Sets the "language" attribute for the tag
   *
   * @param string
   * @return this
   */
  public function setLanguage($val)
  {
    $this->attribute()->language = $val;

    return $this;
  }

  /**
   * Set the source URL for external scripts
   *
   * @param string
   * @return this
   */
  public function setURL($jsFile)
  {
    $this->attribute()->src = $jsFile;

    return $this;
  }

  /**
   * Sets the "type" attribute for the tag
   *
   * @param string
   * @return this
   */
  public function setType($val)
  {
    $this->attribute()->type = $val;

    return $this;
  }
}
