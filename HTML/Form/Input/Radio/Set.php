<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input\Radio;

/**
 * A collection of Radio tags to set them up as a group to include into a Form
 * object
 */
class Set extends \Metrol\HTML\Form\Tag
{
  /**
   * Values used to set up how text will relate to the radio boxes.
   *
   * @const
   */
  const TEXT_LEFT  = 1;
  const TEXT_RIGHT = 2;
  const TEXT_NONE  = 3;

  /**
   * How labels will be displayed with the radio tags
   *
   * @var integer
   */
  private $textAlign;

  /**
   * The collection of radio tags
   *
   * @var array
   */
  private $radioTags;

  /**
   * Set of labels for the radio tags
   *
   * @var array
   */
  private $labelTags;

  /**
   * The value that to be marked as selected.
   *
   * @var string
   */
  private $selectedValue;

  /**
   * The field name to be used on all the radio tags
   *
   * @var string
   */
  private $fieldName;

  /**
   * The delimeter used between the label/radio tag combinations
   *
   * @var string
   */
  private $delimeter;

  /**
   * Delimeter between the label and radio
   *
   * @var string
   */
  private $labelDelim;

  /**
   * Initialize the Radio Set object
   *
   * @param string Field name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('input', self::CLOSE_SELF);

    $this->textAlign     = self::TEXT_RIGHT;
    $this->radioTags     = array();
    $this->labelTags     = array();
    $this->delimeter     = "<br />\n";
    $this->labelDelim    = '&nbsp;';
    $this->selectedValue = '';

    $this->setFieldName($fieldName);
  }

  public function __toString()
  {
    return $this->output();
  }

  /**
   * Produce the output of this object
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    foreach ( $this->radioTags as $val => $radio )
    {
      $label = $this->labelTags[$val];

      if ( $val == $this->selectedValue )
      {
        $radio->setCheck(true);
      }

      switch ($this->textAlign)
      {
        case self::TEXT_LEFT:
          $rtn .= $label.$this->labelDelim.$radio;
          $rtn .= $this->delimeter;
          break;

        case self::TEXT_RIGHT:
          $rtn .= $radio.$this->labelDelim.$label;
          $rtn .= $this->delimeter;
          break;

        case self::TEXT_NONE:
          $rtn .= $radio;
          $rtn .= $this->delimeter;
          break;

        default:
          $rtn .= $radio.' '.$label;
          $rtn .= $this->delimeter;
          break;
      }
    }

    return $rtn;
  }

  /**
   * Override the set field name so it can be applied to all the tags
   *
   * @param string Field name to assign to string
   * @return this
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;

    return $this;
  }

  /**
   * Overide the setID method.  This class takes control of IDs for radio tags
   * to insure that labels match up properly.
   *
   * @param string
   * @return this
   */
  public function setID($idName)
  {
    $idName = null;
    return $this;
  }

  /**
   * Take the usual setValue call and just store it so that it can be properly
   * applied when the output is generated.
   *
   * @param string
   * @return this
   */
  public function setValue($val)
  {
    $this->selectedValue = $val;

    return $this;
  }

  /**
   * Takes in an array of values to create radio tags and labels from them.
   * Keys will be assigned to the value of the tag.  Array values will be
   * assigned to the labels.
   *
   * @param array List of values
   * @return this
   */
  public function addArray(array &$keyVals)
  {
    foreach ( $keyVals as $value => $label )
    {
      $this->addItem($value, $label);
    }

    return $this;
  }

  /**
   * Takes a Value and Label to create the appropriate objects internally to be
   * added to the stack of tags to display
   *
   * @param mixed Value to assign to the Radio tag
   * @param string Label to assign to that value
   * @return this
   */
  public function addItem($value, $labelText)
  {
    $radio = new \Metrol\HTML\Form\Input\Radio($this->fieldName);
    $label = new \Metrol\HTML\Form\Label;

    // Values and labels in place
    $radio->setValue($value);
    $label->setContent($labelText);

    // Associate the two
    $tagID = uniqid($this->fieldName.'-');
    $radio->setID($tagID);
    $label->setField($tagID);

    // Now store them in their respective arrays
    $this->radioTags[$value] = $radio;
    $this->labelTags[$value] = $label;

    return $this;
  }

  /**
   * Specifies if the text should be aligned to the right, left, or not show up
   * at all in relation to the radio tag.
   *
   * @param integer Alignment value
   * @return this
   */
  public function setTextAlign($align)
  {
    switch ($align)
    {
      case self::TEXT_LEFT:
        $this->textAlign = self::TEXT_LEFT;
        break;

      case self::TEXT_RIGHT:
        $this->textAlign = self::TEXT_RIGHT;
        break;

      case self::TEXT_NONE:
        $this->textAlign = self::TEXT_NONE;
        break;

      default: // Back to the class default
        $this->textAlign = self::TEXT_RIGHT;
        break;
    }

    return $this;
  }

  /**
   * Sets the string that will show up after the radio/label pair in the list.
   * The class default is to put a <br /> tag with a line feed.
   *
   * @param string
   * @return this
   */
  public function setDelimeter($delimText)
  {
    $this->delimeter = $delimText;

    return $this;
  }

  /**
   * Sets the string that shows up between the label and radio tag.
   * The class default is a non-breaking space.
   *
   * @param string
   * @return this
   */
  public function setLabelDelimeter($delimText)
  {
    $this->labelDelim = $delimText;

    return $this;
  }
}
