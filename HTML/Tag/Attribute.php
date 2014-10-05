<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Tag;

/**
 * Handles all the attributes that can be stored in a tag.
 */
class Attribute
{
  /**
   * The list of attributes and their values
   *
   * @var array
   */
  private $attribs;

  /**
   * A list of styles that are kept separate from the rest of the attributes
   * until assembled.
   *
   * @var array
   */
  private $styles;

  /**
   *
   */
  public function __construct()
  {
    $this->attribs = array();
    $this->styles  = array();
  }

  /**
   * Used to set attributes as though they were member vars.
   *
   * @param string
   * @param string
   */
  public function __set($attrib, $value)
  {
    $this->set($attrib, $value);
  }

  /**
   * Get back the value for an attribute
   *
   * @param string
   * @return string
   */
  public function __get($attrib)
  {
    return $this->get($attrib);
  }

  /**
   * Produce the attributes into a single string suitable for insertion into
   * a Tag object.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->assemble();
  }

  /**
   * Report on how many attributes have been defined
   *
   * @return integer
   */
  public function count()
  {
    $rtn = 0;

    $rtn += count($this->attribs);

    if ( count($this->styles) > 0 )
    {
      $rtn++;
    }

    return $rtn;
  }

  /**
   * Determine if an attribute exists or not
   *
   * @param string
   * @return boolean
   */
  public function exists($key)
  {
    if ( array_key_exists($key, $this->attribs) )
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Provide the value associated with an attribute.
   *
   * @param string
   * @return string
   */
  public function get($key)
  {
    if ( array_key_exists($key, $this->attribs) )
    {
      return $this->attribs[$key];
    }
  }

  /**
   * Use this to insure that the attribute being passed in only exists a single
   * time within a tag
   *
   * @param string
   * @param string
   */
  public function set($key, $value)
  {
    $key = strtolower($key);

    if ( $key == "class" )
    {
      $this->addClass($value);
    }

    if ( $key == 'style' )
    {
      $this->addStyle($value);

      return;
    }

    $this->attribs[$key] = $value;
  }

  /**
   * To be used by all the various attribute methods to get their attributes
   * properly added to the stack.
   *
   * @param string
   * @param string
   */
  public function add($key, $value)
  {
    $key = strtolower($key);

    if ( $key == "class" )
    {
      $this->addClass($value);
    }

    if ( $key == "style" )
    {
      $this->addStyle($value);

      return;
    }

    $this->attribs[$key] = $value;
  }

  /**
   * Removes an attribute from the tag based on its name
   *
   * @param string
   */
  public function delete($key)
  {
    if ( array_key_exists($key, $this->attribs) )
    {
      unset($this->attribs[$key]);
    }
  }

  /**
   * Adds a CSS Class to the attribute stack.
   * These are a little different, as call to this needs to add to the class
   * attribute rather than replace it.
   *
   * @param string
   */
  protected function addClass($className)
  {
    if ( array_key_exists("class", $this->attribs) )
    {
      $this->attribs['class'] = $this->attribs['class'].' '.$className;
    }
    else
    {
      $this->attribs['class'] = $className;
    }
  }

  /**
   * Adds a CSS Style to the stack.
   *
   * This expects an input that looks like:
   * 'border: 1px'
   *
   * @param string
   */
  public function addStyle($style, $value)
  {
    $this->styles[$style] = $value;
  }

  /**
   * Deletes the specified style from the stack
   *
   * @param string
   */
  public function deleteStyle($style)
  {
    if ( array_key_exists($style, $this->styles) )
    {
      unset($this->styles[$style]);
    }
  }

  /**
   * Assembles all the attributes into a string ready to go into a tag's
   * opening
   *
   * @return string
   */
  private function assemble()
  {
    $rtn = '';

    foreach ( $this->attribs as $key => $val )
    {
      $rtn .= $key.'="'.$val.'" ';
    }

    if ( strlen($rtn) > 0 )
    {
      $rtn = ' '.trim($rtn);
    }

    if ( count($this->styles) > 0 )
    {
      $rtn .= ' style="';
      $styleStr = '';

      foreach ( $this->styles as $key => $val )
      {
        $styleStr .= "$key: $val; ";
      }

      $rtn .= trim($styleStr).'"';
    }

    return $rtn;
  }
}
