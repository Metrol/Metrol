<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\CSS\Declaration;

/**
 * Define a block of declarations for a CSS rule
 *
 */
class Block
{
  /**
   * List of declaration blocks
   *
   * @var array
   */
  private $declarations;

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
    $this->declarations = array();
  }

  /**
   * Produce the declaration output
   *
   * @return string
   */
  public function output()
  {
    if ( empty($this->declarations) )
    {
      return '';
    }

    $rtn = '{';

    if ( !$this->compress )
    {
      $rtn .= "\n";
    }

    foreach ( $this->declarations as $declaration )
    {
      if ( !$this->compress )
      {
        $rtn .= '  ';
      }

      $rtn .= $declaration->setCompress($this->compress)->output(true);

      if ( !$this->compress )
      {
        $rtn .= "\n";
      }
    }

    $rtn .= '}';

    if ( !$this->compress )
    {
      $rtn .= "\n\n";
    }

    return $rtn;
  }

  /**
   * Sets a new declaration with a property and value
   *
   * @param string $prop CSS Property
   * @param string $val  Value to assign the property
   *
   * @return this
   */
  public function addDeclaration($prop, $val)
  {
    $declare = new \Metrol\CSS\Declaration;
    $declare->setProperty($prop, $val);

    $this->declarations[] = $declare;

    return $this;
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
