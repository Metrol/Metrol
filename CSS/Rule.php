<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\CSS;

/**
 * Define a single CSS rule
 *
 */
class Rule
{
  /**
   * The declaration block
   *
   * @var \Metrol\CSS\Declaration\Block
   */
  private $decBlock;

  /**
   * The style selector
   *
   * @var \Metrol\CSS\Selector
   */
  private $selector;

  /**
   * Determines if the output of this class should be stripped of extra white
   * space that isn't needed.
   *
   * @var boolean
   */
  private $compress;

  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    $this->decBlock = new \Metrol\CSS\Declaration\Block;
    $this->selector = new \Metrol\CSS\Selector;
    $this->compress = false;
  }

  /**
   * Produce the output from this object
   *
   * @return string
   */
  public function output()
  {
    $rtn = $this->selector->setCompress($this->compress)->output();

    if ( !$this->compress )
    {
      $rtn .= "\n";
    }

    $rtn .= $this->decBlock->setCompress($this->compress)->output();

    return $rtn;
  }

  /**
   * Provide the Selector object for this rule
   *
   * @return \Metrol\CSS\Selector
   */
  public function getSelector()
  {
    return $this->selector;
  }

  /**
   * Provide the declaration block for this rule
   *
   * @return \Metrol\CSS\Declaration\Block
   */
  public function getDeclarationBlock()
  {
    return $this->decBlock;
  }

  /**
   * Sets the flag to compress the output as much as possible
   *
   * @param boolean
   * 
   * @return this
   */
  public function setCompress($flag)
  {
    if ( $flag )
    {
      $this->compress = true;
    }
    else
    {
      $this->compress = false;
    }

    return $this;
  }
}
