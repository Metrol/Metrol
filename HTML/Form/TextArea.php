<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * A TextArea form object
 */
class TextArea extends Tag
{
  /**
   * Some defaults for the text area tag
   *
   * @const
   */
  const DEF_ROWS = 5;
  const DEF_COLS = 40;
  const DEF_WRAP = 'soft';

  /**
   * Initialize the TextArea object
   *
   * @param string Field Name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('textarea', self::CLOSE_CONTENT);

    $this->setFieldName($fieldName)
         ->setColumnWidth(self::DEF_COLS)
         ->setRows(self::DEF_ROWS)
         ->setWrap(self::DEF_WRAP);
  }

  /**
   * Override the set value as the value actually needs to go in as content of
   * this tag.
   *
   * @param string
   * @return this
   */
  public function setValue($val)
  {
    $this->setContent( htmlentities($val) );

    return $this;
  }

  /**
   * Used to put some sample text into the content that vanishes when the
   * user clicks in
   *
   * @param string
   * @return this
   */
  public function setPlaceholder($text)
  {
    $this->attribute()->placeholder = htmlentities($text);

    return $this;
  }

  /**
   * Specifies the maximum length of the area
   *
   * @param integer
   * @return this
   */
  public function setMaxLength($maxChars)
  {
    $this->attribute()->maxlength = intval($maxChars);

    return $this;
  }

  /**
   * Specifies the width in column characters to use
   *
   * @param integer
   * @return this
   */
  public function setColumnWidth($width)
  {
    $this->attribute()->cols = intval($width);

    return $this;
  }

  /**
   * Specifies the number or rows of characters
   *
   * @param integer
   * @return this
   */
  public function setRows($rows)
  {
    $this->attribute()->rows = intval($rows);

    return $this;
  }

  /**
   * Sets the word wrap attribute.  Either Hard or Soft.
   *
   * @param string Hard|Soft
   * @return this
   */
  public function setWrap($wrap)
  {
    $wrap = strtolower($wrap);

    switch ($wrap)
    {
      case 'soft':
        $this->attribute()->wrap = 'soft';
        break;

      case 'hard':
        $this->attribute()->wrap = 'hard';

      default:
        $this->attribute()->wrap = 'soft';
        break;
    }

    return $this;
  }
}
