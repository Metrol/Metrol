<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Similar to a Text input, but allows for suggested values to be provided as
 * the user types.
 */
class TextList extends Text
{
  /**
   * List of option tags that make up the datalist
   *
   * @var array
   */
  protected $optionTags;

  /**
   * @param field name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->setInputType('list');

    $this->optionTags = array();
  }

  /**
   * Extend the parent output to include the list of options to suggest
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';
    
    if ( count($this->optionTags) > 0 )
    {
      $fieldName = $this->attribute()->name;
      $listID = uniqid($fieldName.'-');

      $this->attribute()->list = $listID;

      $rtn .= "<datalist id=\"$listID\">\n";

      foreach ( $this->optionTags as $opt )
      {
        $rtn .= '  '.$opt."\n";
      }

      $rtn .= "</datalist>\n";
    }

    $rtn = parent::output()."\n".$rtn;

    return $rtn;
  }

  /**
   * Adds a list of items to the stack to be used to suggest a value to the
   * user.
   *
   * @param array
   * @return this
   */
  public function addArray(array &$values)
  {
    foreach ( $values as $value )
    {
      $this->addItem($value);
    }

    return $this;
  }

  /**
   * Adds an item to the list of options the user will be provided when typing
   *
   * @param mixed
   * @return this
   */
  public function addItem($value)
  {
    $opt = new \Metrol\HTML\Form\Select\Option;
    $opt->setValue($value);

    $this->optionTags[] = $opt;
  }
}
