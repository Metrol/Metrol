<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table;

/**
 * Defines the Caption for a Table
 */
class Caption extends \Metrol\HTML\Tag
{
  /**
   * @param string
   */
  public function __construct($text = '')
  {
    parent::__construct('caption', self::CLOSE_CONTENT);

    $this->setContent($text);
  }

  public function __toString()
  {
    return $this->output();
  }

  public function output()
  {
    $rtn = '';
    $content = $this->getContent();

    if ( strlen($content) > 0 )
    {
      $rtn .= $this->open();
      $rtn .= $content;
      $rtn .= $this->close();
      $rtn .= "\n";
    }

    return $rtn;
  }
}
