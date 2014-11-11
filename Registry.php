<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * All the evils of a global namespace all wrapped up into the disguise of
 * a genuine class.  Oh yes, it's a singleton!
 *
 */
class Registry
{
  /**
   * Singleton reference back to this object
   *
   * @var \Metrol\Registry
   */
  private static $singObj;

  /**
   * Values in this registry
   *
   * @var \Metrol\Data\Item
   */
  protected $item;

  /**
   * Initialize the Registry object
   *
   * @param object
   */
  protected function __construct()
  {
    if ( !is_object($this->item) )
    {
      $this->item = new \Metrol\Data\Item;
    }
  }

  /**
   * Instantiates the object as needed
   *
   * @return \Metrol\Registry
   */
  public static function init()
  {
    if ( !is_object(self::$singObj) )
    {
      self::$singObj = new Registry;
    }

    return self::$singObj;
  }

  /**
   * Produces a diagnostic output from this object
   *
   */
  public static function dump()
  {
    $reg = self::init();
    $msg = 'Diagnostic Dump from '.get_class($reg)."<hr />\n";
    $msg .= strval($reg->item);

    print $msg;
  }

  /**
   * A diagnostic dump of the values stored here.
   *
   * @return string
   */
  public function __toString()
  {
    return strval($this->item);
  }

  /**
   * Pass through to the data item object
   *
   * @param string
   * @param mixed
   */
  public function __set($field, $value)
  {
    $this->item->setValue($field, $value);
  }

  /**
   * Pass through to the data item object
   *
   * @param string
   *
   * @return mixed
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
   * Provide a static method for setting a value
   *
   * @param string
   * @param mixed
   */
  public static function setValue($field, $value)
  {
    $reg = self::init();

    $reg->item->setValue($field, $value);
  }

  /**
   * Provide a static method for getting a value
   *
   * @param string
   *
   * @return mixed
   */
  public static function getValue($field)
  {
    $reg = self::init();

    return $reg->item->$field;
  }
}
