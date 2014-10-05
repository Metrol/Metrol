<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data;

/**
 * Base class to describe a data structure dynamically
 */
class Structure extends Item
{
  /**
   * The list of type definitions that define what can be stored here.
   *
   * @var array
   */
  protected $typeDefs;

  /**
   * Instantiate the Structure
   */
  public function __construct()
  {
    parent::__construct();

    $this->typeDefs = array();
  }

  /**
   * Used to define what kinds of types this object will be supporting
   *
   * @param string Name of the field
   * @param \Metrol\Data\Type
   */
  public function addFieldDefinition($name, \Metrol\Data\Type $type)
  {
    $this->typeDefs[$name] = $type;
  }

  /**
   * @param string Field name to set
   * @param mixed
   */
  public function setValue($field, $value)
  {
    if ( array_key_exists($field, $this->typeDefs) )
    {
      $cleanVal = $this->typeDefs[$field]->boundsValue($value);

      $this->dataItem[$field] = $cleanVal;
    }
  }

  /**
   * @param string Field name being requested
   * @return mixed
   */
  public function getValue($field)
  {
    $rtn = null;

    if ( array_key_exists($field, $this->dataItem) )
    {
      $rtn = $this->dataItem[$field];
    }
  }
}
