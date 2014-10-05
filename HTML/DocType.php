<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Handles creating the DOCTYPE tag that appears before the HTML tag
 */
class DocType
{
  /**
   * Define the supported Doc Types
   *
   * @const
   */
  const HTML_V4_TRAN    = 0;
  const XHTML_V1_STRICT = 1;
  const XHTML_V1_TRAN   = 2;
  const HTML_V5         = 3;

  /**
   * Which defined type to use
   *
   * @param integer
   */
  private $docType;

  /**
   * Which language to define for this page?
   *
   * @param string
   */
  private $language;

  /**
   * Specify the type of document and language to use.
   *
   * @param integer
   * @param string
   */
  public function __construct($docType, $language)
  {
    $this->setDocType($docType);
    $this->setLanguage($language);
  }

  /**
   * Produce the DocType tag
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = "";
    $lang = strtoupper($this->language);

    switch ($this->docType)
    {
      case self::XHTML_V1_TRAN:
        $rtn  = "<!DOCTYPE html ";
        $rtn .= "PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//$lang\" ";
        $rtn .= "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
        break;

      case self::XHTML_V1_STRICT:
        $rtn  = "<!DOCTYPE html ";
        $rtn .= "PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//$lang\" ";
        $rtn .= "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";

      case self::HTML_V4_TRAN:
        $rtn  = "<!DOCTYPE HTML ";
        $rtn .= "PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//$lang\" ";
        $rtn .= "\"http://www.w3.org/TR/html4/loose.dtd\">";
        break;

      case self::HTML_V5:
        $rtn  = '<!DOCTYPE html>';
        break;

      default:
        $rtn  = "<!DOCTYPE HTML ";
        $rtn .= "PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//$lang\" ";
        $rtn .= "\"http://www.w3.org/TR/html4/loose.dtd\">";
    }

    return $rtn;
  }

  /**
   * Sets the Document Type to one of the defined constant types.
   *
   * @param integer
   */
  public function setDocType($docType)
  {
    $docType = intval($docType);

    switch ($docType)
    {
      case self::HTML_V4_TRAN:
        $this->docType = $docType;
        break;

      case self::XHTML_V1_STRICT:
        $this->docType = $docType;
        break;

      case self::XHTML_V1_TRAN:
        $this->docType = $docType;
        break;

      case self::HTML_V5:
        $this->docType = $docType;
        break;

      default:
        $this->docType = self::HTML_V4_TRAN;
    }
  }

  /**
   * Provides which doc type we're set to
   *
   * @return integer
   */
  public function getDocType()
  {
    return $this->docType;
  }

  /**
   * Sets the language to be defined for the rest of the document
   *
   * @param string
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }

  /**
   * Provides the language that was defined for this doc type
   *
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
}
