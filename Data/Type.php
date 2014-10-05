<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data;

/**
 * Describes a data type and provides bounds checkings for that type.
 * This is a parent for all the kinds of types that might exist in a
 * dynamic structure.
 */
class Type
{
  /**
   * The field name being associated with this type
   *
   * @param string
   */
  protected $fieldName;

  /**
   * Is this allowed to be a null value when storing?
   *
   * @var boolean
   */
  protected $nullOk;

  /**
   * Instantiate the Type
   */
  public function __construct()
  {
    $this->nullOk = false;
  }

  /**
   * Sets the name of this field
   *
   * @param string
   */
  public function setFieldName($name)
  {
    $this->fieldName = $name;
  }

  /**
   * Gets the name of this field
   *
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }

  /**
   * To be overridden by the specific types.
   *
   * @param mixed
   * @return mixed
   */
  public function boundsValue($value)
  {
    return $value;  // The world's worst data cleansing routine.
  }

  /**
   * Sets if this value is allowed to be null.  Only used for going into a
   * data store.
   *
   * @param boolean
   */
  public function setNullOk($flag)
  {
    if ( $flag )
    {
      $this->nullOk = true;
    }
    else
    {
      $this->nullOk = false;
    }
  }

  /**
   * Provides whether a null is okay here or not
   *
   * @return boolean
   */
  public function isNullOk()
  {
    return $this->nullOk;
  }
}
