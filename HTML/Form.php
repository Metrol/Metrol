<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Provides the entire context of an HTML form, allowing to pre-store values
 * in this object that can later be provided to Views.
 */
class Form
{
  /**
   * The form opening tag
   *
   * @var \Metrol\HTML\Form\Open
   */
  protected $openTag;

  /**
   * This list of the fields that make up the over all form
   *
   * @var array
   */
  protected $fields;

  /**
   * Keeps a copy of the validation key hidden tag used to verify the origin of
   * the form
   *
   * @var \Metrol\HTML\Form\Hidden
   */
  protected $verifyTag;

  /**
   * This will flag whether to retain the previous value as a hidden
   * form field
   *
   * @var boolean
   */
  protected $keepOriginalFlag;

  /**
   * Used to prefix field names with some value.  Handy for multi-table forms
   *
   * @var string
   */
  protected $fieldPrefix;

  /**
   * What to prefix the field values with when storing origal values
   *
   * @var string
   */
  protected $originalPrefix;

  /**
   * Initialize the Form object
   *
   */
  public function __construct()
  {
    $this->fields = array();

    // Defaults for not putting original value storage on to the form.
    $this->keepOriginalFlag = false;
    $this->originalPrefix   = 'orig_';

    // When you need to have a custom prefix assigned to field names.
    $this->fieldPrefix      = '';

    // Initial the form verification tag that can be used to provide a little
    // bit of warm and fuzzy that the form processed is friendly one.
    $this->setSessionCheckSum('ThisIsAKeyForTheMetrolFormObject');

    // Initialize the form open tag
    $this->openTag = new \Metrol\HTML\Form\Open;
    $this->openTag->setName('editForm')
                  ->setActionURL('./')
                  ->setMethod('post');
  }

  /**
   * Provide back form field objects as though they were member vars
   *
   * @param string Field name for the field object to retrieve
   * @return \Metrol\HTML\Form\Field
   */
  public function __get($fieldName)
  {
    $rtn = $this->getField($fieldName);

    return $rtn;
  }

  /**
   * Check to see if the the requested field name exists
   *
   * @param string Field name for the field object to retrieve
   * @return boolean
   */
  public function __isset($fieldName)
  {
    $rtn = false;

    if ( array_key_exists($fieldName, $this->fields) )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Initiates a new field object for this form
   *
   * @param string Field name for the field object
   * @return \Metrol\HTML\Form\Field
   */
  public function createField($fieldName)
  {
    $this->fields[$fieldName] = new Form\Field($fieldName);

    $this->fields[$fieldName]->setFieldPrefix($this->fieldPrefix);
    $this->fields[$fieldName]->setKeepOriginal($this->keepOriginalFlag);
    $this->fields[$fieldName]->setOriginalPrefix($this->originalPrefix);

    return $this->fields[$fieldName];
  }

  /**
   * Provides back a field based on the field name
   *
   * @param string Field name for the field object
   * @return \Metrol\HTML\Form\Field
   */
  public function getField($fieldName)
  {
    $rtn = null;

    if ( array_key_exists($fieldName, $this->fields) )
    {
      $rtn = $this->fields[$fieldName];
    }

    return $rtn;
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

    return $this;
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
   * Call up the open form tag.
   *
   * @return \Metrol\HTML\Form\Open
   */
  public function open()
  {
    return $this->openTag;
  }

  /**
   * Provide the closing tag for the form.
   *
   * @return string
   */
  public function close()
  {
    return '</form>';
  }

  /**
   * Provides a submit button for the form
   *
   * @return \Metrol\HTML\Form\Submit
   */
  public function submitButton($buttonText = 'Submit')
  {
    $btn = new Form\Input\Submit;
    $btn->setValue($buttonText);

    return $btn;
  }

  /**
   * Output the verify hidden field
   *
   * @return \Metrol\HTML\Form\Input\Hidden
   */
  public function checksum()
  {
    return $this->verifyTag;
  }

  /**
   * Sets a form field called formCheckSum that uses the sessionID as
   * a way to check the form.
   *
   * @param \Metrol\HTTP\Session
   * @param string
   */
  public function setSessionCheckSum($key, $fieldName='formCheckSum')
  {
    if ( !isset($_SESSION) )
    {
      return;
    }

    $mte = new \Metrol\Text\Encryption($key);

    $sessID = \session_id();
    $randChars = $mte->genRandomString(7);

    $textVal = $randChars.'-+-'.$sessID;
    $textEnc = $mte->encode($textVal);

    $this->verifyTag = new Form\Input\Hidden($fieldName);
    $this->verifyTag->setValue($textEnc);
  }

  /**
   * Checks the encrypted text passed in and compares it agains the session ID
   * to see if what came back was valid.
   *
   * @param string
   * @param string
   * @return boolean
   */
  public function validateCheckSum($key, $encText)
  {
    if ( !isset($_SESSION) )
    {
      return true;
    }

    $mte = new \Metrol\Text\Encryption($key);
    $sessID = \session_id();

    $decText = $mte->decode($encText);

    if ( strpos($decText, '-+-') === false )
    {
      return false;
    }

    $testID = substr($decText, strpos($decText, '-+-') + 3);

    if ( $sessID == $testID )
    {
      return true;
    }

    return false;
  }
}
