<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * Define the Select tag
 */
class Select extends Tag
{
  /**
   * The list of Option tags that have been created for this object
   *
   * @var array
   */
  protected $options;

  /**
   * List of selected options based on the value of the option
   *
   * @var array
   */
  protected $selectedOptions;

  /**
   * Specifies a default selection text to show when nothing is selected
   *
   * @var string
   */
  protected $defaultText;

  /**
   * A value to go with the default text.  Only applicable when the default
   * text has some value.
   *
   * @var string
   */
  protected $defaultValue;

  /**
   * When set, the default text and value will show up in the list even if
   * something else is selected.
   *
   * @var boolean
   */
  protected $forceDefaultFlag;

  /**
   * Pass in the field name of the select box
   *
   * @param string
   */
  public function __construct($fieldName = '')
  {
    parent::__construct('select', self::CLOSE_CONTENT);

    $this->setFieldName($fieldName);

    $this->selectedOptions = array();
    $this->options         = array();

    $this->defaultText  = '';
    $this->defaultValue = '';
    $this->forceDefaultFlag = false;
  }

  /**
   * Produce the complete dropdown area that this class is famous for!
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = $this->open();
    $rtn .= "\n";

    $option = $this->getDefaultOption();

    if ( $option !== null )
    {
      $rtn .= '  '.$option."\n";
    }

    foreach ( $this->options as $value => $option )
    {
      if ( in_array($value, $this->selectedOptions) )
      {
        $option->setSelected(true);
      }
      else
      {
        $option->setSelected(false);
      }

      $rtn .= '  '.$option."\n";
    }

    $rtn .= $this->close();

    return $rtn;
  }

  /**
   * Specifies if this is a select multiple kind of box.
   *
   * @return this
   */
  public function setMultiple()
  {
    $this->attribute()->multiple = 'multiple';

    return $this;
  }

  /**
   * Specify the select tag is a single selection only box.
   *
   * @return this
   */
  public function setSingle()
  {
    $this->attribute()->delete('multiple');

    return $this;
  }

  /**
   * Specifies how many rows to show
   *
   * @param integer
   * @return this
   */
  public function setSize($rows)
  {
    $rows = intval($rows);

    // Must have at least 1 visible row
    if ( $rows < 1 )
    {
      $rows = 1;
    }

    $this->attribute()->size = $rows;

    return $this;
  }

  /**
   * Sets the default text and value to show up at the top of the list
   *
   * @param string The default text visible to the user
   * @param string The value inside the option tag
   * @return this
   */
  public function setDefault($text, $value = '')
  {
    $this->defaultText  = $text;
    $this->defaultValue = $value;

    return $this;
  }

  /**
   * A true value here will force the default text/value to show up even if
   * there are selected options.
   * A false indicates that the default only shows up if nothing is selected.
   * This does nothing if there is no default text set.
   *
   * @param boolean
   * @return this
   */
  public function setForceDefault($flag)
  {
    if ( $flag )
    {
      $this->forceDefaultFlag = true;
    }
    else
    {
      $this->forceDefaultFlag = false;
    }

    return $this;
  }

  /**
   * Provides an Option tag based on the value of that option.
   *
   * @param string
   * @return \Metrol\HTML\Form\Select\Option
   */
  public function getOption($optionValue)
  {
    $rtn = null;

    if ( array_key_exists($optionValue, $this->options) )
    {
      $rtn = $this->options[$optionValue];
    }

    return $rtn;
  }

  /**
   * Add a new item to the list with visible contents and it's value.
   *
   * @param string
   * @param string
   * @return \Metrol\HTML\Form\Select\Option
   */
  public function addItem($visibleContent, $value)
  {
    $option = new Select\Option;
    $option->setContent($visibleContent)
           ->setValue($value);

    $this->options[$value] = $option;

    return $option;
  }

  /**
   * An array passed in here will be added as Options to the drop down.
   * arr[index] = value converts to <option value="index">value</option>
   *
   * @param array
   * @return this
   */
  public function addArray(array $dataList)
  {
    foreach ( $dataList as $key => $val )
    {
      $this->addItem($val, $key);
    }

    return $this;
  }

  /**
   * Adds values that are to be marked selected when this area is assembled.
   * This can be called multiple times.  Every value that is actually in the
   * list of options will be marked selected.
   *
   * @param string Option value to select
   * @return this
   */
  public function setSelected($optionValue)
  {
    if ( $optionValue === null )
    {
      return $this;
    }

    if ( !in_array($optionValue, $this->selectedOptions) )
    {
      $this->selectedOptions[] = $optionValue;
    }

    return $this;
  }

  /**
   * Override the parent method to direct the requested value to setSelected
   *
   * @param string Value that is selected
   * @return this
   */
  public function setValue($value)
  {
    $this->setSelected($value);

    return $this;
  }

  /**
   * Provides the toString() with the default option when specified.
   *
   * @return null|\Metrol\HTML\Option
   */
  protected function getDefaultOption()
  {
    if ( strlen($this->defaultText) == 0 )
    {
      return null;
    }

    if ( count($this->selectedOptions) == 0 )
    {
      $option = new Select\Option;
      $option->setContent($this->defaultText)
             ->setValue($this->defaultValue)
             ->setSelected(true);

      return $option;
    }

    if ( count($this->selectedOptions) > 0 and $this->forceDefaultFlag )
    {
      $option->setContent($this->defaultText)
             ->setValue($this->defaultValue);

      return $option;
    }

    return null;
  }
}
