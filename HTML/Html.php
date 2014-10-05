<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the Html tag
 */
class Html extends Tag
{
  /**
   */
  public function __construct()
  {
    parent::__construct('html', self::CLOSE_NONE);
  }

  /**
   * Allows a call to pass in a Doc Type object to readily define attributes
   * that should show up to support it.
   *
   * @param \Metrol\HTML\DocType
   */
  public function setDocType(DocType $docType)
  {
    if ( $docType->getDocType() == DocType::XHTML_V1_STRICT )
    {
      $lang = strtolower($docType->getLanguage());
      $this->attribute()->set('xmlns', 'http://www.w3.org/1999/xhtml');
      $this->attribute()->set('xml:lang', $lang);
    }

    if ( $docType->getDocType() == DocType::XHTML_V1_TRAN )
    {
      $lang = strtolower($docType->getLanguage());
      $this->attribute()->set('xmlns', 'http://www.w3.org/1999/xhtml');
      $this->attribute()->set('xml:lang', $lang);
    }

    return $this;
  }

  /**
   * Set the language set to be used for this page
   *
   * @param string
   */
  public function setLanguage($lang)
  {
    $this->attribute()->set('lang', $lang);

    return $this;
  }
}
