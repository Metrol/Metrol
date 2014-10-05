<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Describes composite of multiple field types as a single field
 */
class Composite extends \Metrol\Data\Type
{
  /**
   * Field Definitions indexed by name
   *
   * @var array
   */
  protected $fieldDefs;

  /**
   * Instantiate the Integer description
   */
  public function __construct()
  {
    parent::__construct();

    $this->fieldDefs = array();
  }

  /**
   * Add a new Field Definition to the stack.
   *
   * @param string Name of the field
   * @param \Metrol\Field\Type
   */
  public function addFieldDefinition($name, \Metrol\Field\Type $typeDef)
  {
    $this->fieldDefs[$name] = $typeDef;
  }
}
