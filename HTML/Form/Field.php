<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * Allows the user to create a form field that will called from the Form class
 */
class Field
{
  /**
   * The name of the field in question
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The HTML tag that makes up this form field
   *
   * @var \Metrol\HTML\Form\Tag
   */
  protected $formTag;

  /**
   * Used to maintain a list of tags if this has been defined as an array of
   * fields.
   *
   * @var array
   */
  protected $tagSet;

  /**
   * The label for this field
   *
   * @var \Metrol\HTML\Form\Label
   */
  protected $label;

  /**
   * A list of labels for an array form
   *
   * @var array
   */
  protected $labelSet;

  /**
   * This will flag whether to retain the previous value as a hidden
   * form field
   *
   * @var boolean
   */
  protected $keepOriginalFlag;

  /**
   * What to prefix the field values with when storing origal values
   *
   * @var string
   */
  protected $originalPrefix;

  /**
   * Used to prefix field names with some value.  Handy for multi-table forms
   *
   * @var string
   */
  protected $fieldPrefix;

  /**
   * Initialize the Field object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName)
  {
    $this->fieldName = $fieldName;
    $this->label     = new Label($fieldName);
    $this->tagSet    = array();
    $this->labelSet  = array();
    $this->formTag   = null;

    // Defaults for putting original value storage on to the form.
    $this->keepOriginalFlag = false;
    $this->originalPrefix   = 'orig_';

    // When you need to have a custom prefix assigned to field names.
    $this->fieldPrefix      = '';
  }

  /**
   * Output of this field object
   *
   * @return string
   */
  public function __toString()
  {
    if ( $this->formTag == null )
    {
      return '';
    }

    $outTag = clone $this->formTag;

    $outName = $this->fieldPrefix.$this->fieldName;
    $outTag->setFieldName($outName);

    $rtn = strval($outTag);

    if ( $this->keepOriginalFlag )
    {
      $origTag = new Hidden($this->originalPrefix.$outName);
      $origTag->setValue($this->formTag->getValue());

      $rtn .= $origTag;
    }

    return $rtn;
  }

  /**
   * Provide the tag object back to modify directly
   *
   * @return \Metrol\HTML\Form\Tag
   */
  public function getTag()
  {
    return $this->formTag;
  }

  /**
   * Sets the type of form field we're working with
   *
   * @param \Metrol\HTML\Form\Tag
   * @return this
   */
  public function setTag(\Metrol\HTML\Form\Tag $tag)
  {
    $this->formTag = $tag;
    $this->formTag->setFieldName($this->fieldPrefix.$this->fieldName)
                  ->setID($this->fieldPrefix.$this->fieldName);

    return $this;
  }

  /**
   * Provide the tag for the specified index
   *
   * @param mixed First key of the field
   * @param mixed Second optional key
   * @return \Metrol\HTML\Form\Tag
   */
  public function getIndex($idx, $idx2 = null)
  {
    if ( $this->formTag == null )
    {
      print "Can't get a tag from this object without first calling setTag().".
            "<br />Exiting....";

      exit;
    }

    $tag = $this->createIndexedTag($idx, $idx2);

    return $tag;
  }

  /**
   * Provide the list of keys from the tagSet.  If a key is specified, then
   * the list of the 2nd dimension of keys is provided.
   *
   * @param integer
   * @return array List of keys
   */
  public function getTagSetKeys($key = null)
  {
    $rtn = array();

    if ( count($this->tagSet) == 0 )
    {
      return $rtn;
    }

    if ( $key == null )
    {
      return array_keys($this->tagSet);
    }

    $key = intval($key);

    if ( array_key_exists($key, $this->tagSet) )
    {
      return array_keys($this->tagSet[$key]);
    }

    return $rtn;
  }

  /**
   * Sets the text for the label for this field
   *
   * @param string Text to show up for the Label tag
   * @return this
   */
  public function setLabelText($labelText, $idx = null, $idx2 = null)
  {
    $label = $this->getLabel($idx, $idx2);

    $label->setHTMLContent($labelText);

    return $this;
  }

  /**
   * Provides the label tag for this field
   *
   * @param integer Key index as needed
   * @param integer Key index as needed
   * @return \Metrol\HTML\Form\Label
   */
  public function getLabel($idx = null, $idx2 = null)
  {
    if ( $idx === null )
    {
      $this->label->setField($this->formTag->attribute()->name);

      return $this->label;
    }

    $label = $this->createIndexedLabel($idx, $idx2);

    return $label;
  }

  /**
   * For when you need to prefix all the field names to prevent name conflicts.
   *
   * @param string
   * @return this
   */
  public function setFieldPrefix($prefix)
  {
    $this->fieldPrefix = $prefix;
  }

  /**
   * Enables/Disables the keep original value functionality.
   * This will place a hidden input box with the value of the original object
   * using the specified field name with a special prefix.
   *
   * @param boolean
   * @return this
   */
  public function setKeepOriginal($flag)
  {
    if ( $flag )
    {
      $this->keepOriginalFlag = true;
    }
    else
    {
      $this->keepOriginalFlag = false;
    }

    return $this;
  }

  /**
   * When keeping original values a prefix is given to the field name of the
   * hidden form object.  This allows you to set that prefix.
   *
   * @param string
   * @return this
   */
  public function setOriginalPrefix($prefix)
  {
    $this->originalPrefix = $prefix;

    return $this;
  }

  /**
   * Will create a tag at the specified indexes.
   *
   * @param integer
   * @param integer
   * @return \Metrol\HTML\Form\Tag
   */
  private function createIndexedTag($idx, $idx2 = null)
  {
    $rtn = null;

    // Kick back the existing tag if one already exists
    if ( $this->tagKeyExists($idx, $idx2) )
    {
      if ( $idx2 === null )
      {
        return $this->tagSet[$idx];
      }
      else
      {
        return $this->tagSet[$idx][$idx2];
      }
    }

    // Falling through, create a new form entry into the tag set
    $fieldName  = $this->fieldPrefix.$this->fieldName;

    if ( $idx2 === null )
    {
      $fieldName .= '['.$idx.']';
      $this->tagSet[$idx] = clone $this->formTag;
      $this->tagSet[$idx]->setFieldName($fieldName);
      $this->tagSet[$idx]->setID($fieldName);

      $rtn = $this->tagSet[$idx];
    }
    else
    {
      $fieldName .= '['.$idx.']'.'['.$idx2.']';
      $this->tagSet[$idx][$idx2] = clone $this->formTag;
      $this->tagSet[$idx][$idx2]->setFieldName($fieldName);
      $this->tagSet[$idx][$idx2]->setID($fieldName);

      $rtn = $this->tagSet[$idx][$idx2];
    }

    return $rtn;
  }

  /**
   * Creates a label for the tag at the specified indexes.
   * Will error out if the field tag has not already been set.
   *
   * @param integer
   * @param integer
   * @return \Metrol\HTML\Form\Label
   */
  private function createIndexedLabel($idx, $idx2 = null)
  {
    // If a label already exists, pass it back
    if ( $this->labelKeyExists($idx, $idx2) )
    {
      if ( $idx2 === null )
      {
        return $this->labelSet[$idx];
      }
      else
      {
        return $this->labelSet[$idx][$idx2];
      }
    }

    $tag   = $this->getIndex($idx, $idx2);

    $label = clone $this->label;

    $label->setField($tag->attribute()->name);

    if ( $idx2 === null )
    {
      $this->labelSet[$idx] = $label;
    }
    else
    {
      $this->labelSet[$idx][$idx2] = $label;
    }

    return $label;
  }

  /**
   * Internal quick check to see if a tag exists at the specified keys
   *
   * @param integer
   * @param integer
   * @return boolean
   */
  private function tagKeyExists($idx, $idx2 = null)
  {
    $rtn = false;

    if ( array_key_exists($idx, $this->tagSet) )
    {
      $rtn = true;;
    }

    if ( $rtn === true and $idx2 !== null )
    {
      if ( array_key_exists($idx2, $this->tagSet[$idx]) )
      {
        $rtn = true;
      }
      else
      {
        $rtn = false;
      }
    }

    return $rtn;
  }

  /**
   * Internal quick check to see if a label exists at the specified keys
   *
   * @param integer
   * @param integer
   * @return boolean
   */
  private function labelKeyExists($idx, $idx2 = null)
  {
    if ( $idx2 === null )
    {
      if ( array_key_exists($idx, $this->labelSet) )
      {
        return true;
      }
      else
      {
        return false;
      }
    }

    $rtn = false;

    if ( array_key_exists($idx, $this->labelSet) )
    {
      if ( array_key_exists($idx2, $this->labelSet[$idx]) )
      {
        $rtn = true;
      }
    }

    return $rtn;
  }
}
