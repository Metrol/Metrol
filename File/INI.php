<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\File;

/**
 * Used to specify information about an INI file
 */
class INI extends Info implements \Iterator
{
  /**
   * The Metrol data item that will store the parsed values
   *
   * @var \Metrol\Data\Item
   */
  protected $item;

  /**
   * Flag to determine whether or not sections need to be processed.
   *
   * @var boolean
   */
  protected $processSectionsFlag;

  /**
   * Set when parse is called to prevent it from happening twice
   *
   * @var boolean
   */
  protected $hasParsed;

  /**
   * Initializes the object
   *
   * @param string Name of the file
   */
  public function __construct($fileName)
  {
    parent::__construct($fileName);

    $this->processSectionsFlag = true;
    $this->hasParsed           = false;

    $this->item = new \Metrol\Data\Item;
  }

  /**
   * Provide item value based on the name
   *
   * @param string
   */
  public function __get($field)
  {
    return $this->item->getValue($field);
  }

  /**
   * Check to see if a value is set here
   *
   * @param string
   */
  public function __isset($field)
  {
    return $this->item->isFieldSet($field);
  }

  /**
   * Remove a field from the data set
   *
   * @param string
   *
   * @return this
   */
  public function unsetField($field)
  {
    $this->item->unsetField($field);

    return $this;
  }

  /**
   * Dump the contents of the Item object when a string is requested
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = strval($this->item);

    return $rtn;
  }

  /**
   * Provides the parsed array from the specified INI file.
   *
   * @return this
   *
   * @throws object \Metrol\Exception
   */
  public function parse()
  {
    if ( $this->hasParsed )
    {
      return $this;;
    }

    if ( !$this->isReadableFile() )
    {
      throw new \Metrol\Exception('INI File Not Ready: '.$this->getRealPath());
    }

    $keyVals = parse_ini_file($this->getRealPath(), $this->processSectionsFlag);

    foreach ( $keyVals as $key => $val )
    {
      if ( is_array($val) )
      {
        $i = new \Metrol\Data\Item;

        foreach ( $val as $k => $v )
        {
          $i->setValue($k, $v);
        }

        $this->item->setValue($key, $i);
      }
      else
      {
        $this->item->setValue($key, $val);
      }
    }

    $this->hasParsed = true;

    return $this;
  }

  /**
   * Specify whether or not sections are used in the INI file.
   *
   * @param boolean
   * @return this
   */
  public function setSectionsFlag($flag)
  {
    if ( $flag )
    {
      $this->processSectionsFlag = true;
    }
    else
    {
      $this->processSectionsFlag = false;
    }

    return $this;
  }

  /**
   * Implementing the Iterartor interface to walk through each field
   *
   */
  public function rewind()
  {
    $this->item->rewind();
  }

  public function current()
  {
    return $this->item->current();
  }

  public function key()
  {
    return $this->item->key();
  }

  public function next()
  {
    return $this->item->next();
  }

  public function valid()
  {
    return $this->item->valid();
  }
}
