<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Defines an array what is a list of a specific type of object.
 * Bringing a little type safety to PHP land.
 */
class ArrayDef extends \Metrol\Data\Type
{
  /**
   * The type of object this is an array of
   *
   * @var \Metrol\Data\Type
   */
  protected $typeDef;

  /**
   * Instantiate the Array definition
   *
   * @param \Metrol\Data\Type
   */
  public function __construct(\Metrol\Data\Type $typeDef)
  {
    parent::__construct();

    $this->typeDef = $typeDef;
  }

  /**
   * Makes sure that all the values in the array are of the defined type
   *
   * @param array
   * @return array
   */
  public function boundsValue($value)
  {
    if ( !is_array($value) )
    {
      return array();
    }

    foreach ( $value as $i => $v )
    {
      if ( !$v instanceOf $this->typeDef )
      {
        unset($value[$i]);
      }
    }
  }
}
