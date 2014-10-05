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
   * Provide an actual set value method
   *
   * @param string
   * @param mixed
   *
   * @return this
   */
  public function setValue($field, $value)
  {
    $this->item->setValue($field, $value);

    return $this;
  }
}
