<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\CSS;

/**
 * A utility for loading up style rules from a string or file into a CSS object
 *
 */
class Parse
{
  /**
   * The CSS object to populate
   *
   * @var \Metrol\CSS
   */
  private $css;

  /**
   * The string to parse and add to the CSS object
   *
   * @var string
   */
  private $styleText;

  /**
   * Instantiate the object
   *
   */
  public function __construct()
  {
    $this->css = new \Metrol\CSS;
    $this->styleText = '';
  }

  /**
   * Set the style text
   *
   * @param string $cssStr The set of CSS rules in a raw string
   *
   * @return this
   */
  public function setStyleText($cssStr)
  {
    $this->styleText = $cssStr;

    return $this;
  }

  /**
   * Specify a CSS file to parse
   *
   * @param string $file Full path and name to the CSS file
   *
   * @return this
   */
  public function setStyleFile($file)
  {
    $cssStr = file_get_contents($file);

    $this->setStyleText($cssStr);

    return $this;
  }

  /**
   * Parse what we've got to to work with and populate the CSS object
   *
   * @return this
   */
  public function parse()
  {
    if ( strlen($this->styleText) == 0 )
    {
      return $this->css;
    }

    $str = $this->removeComments();
    $str = str_replace("\n", ' ', $str);

    $sheetParts = explode("}", $str);

    foreach ( $sheetParts as $sheetPart )
    {
      if ( strpos($sheetPart, '{') === false ) { continue; }

      $rule = $this->css->getNewRule();

      $decBlkParts = explode('{', $sheetPart);

      $selector = $decBlkParts[0];

      $selObj = $rule->getSelector();

      if ( strpos($selector, ',') !== false )
      {
        $selObj->setSelector($selector);
      }
      else
      {
        $selectorBrk = explode(',', $selector);

        foreach ( $selectorBrk as $selPart )
        {
          $selObj->addSelector($selPart);
        }
      }

      $rawRules = $decBlkParts[1];
      $decList = explode(';', $rawRules);

      foreach ( $decList as $rawDec )
      {
        $rawDecTrm = trim($rawDec);
        if ( strpos($rawDecTrm, ':') === false ) { continue; }

        $decParts = explode(':', $rawDecTrm);

        $prop = $decParts[0];
        $val  = $decParts[1];

        $rule->getDeclarationBlock()
             ->addDeclaration($prop, $val);
      }
    }

    return $this;
  }

  /**
   * Fancy bit of regexp to remove the comments from a CSS input
   *
   * @return string
   */
  private function removeComments()
  {
    $buffer = $this->styleText;

    $regex = array(
    "`^([\t\s]+)`ism"=>'',
    "`^\/\*(.+?)\*\/`ism"=>"",
    "`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
    "`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
    "`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n");

    $buffer = preg_replace(array_keys($regex), $regex, $buffer);

    return $buffer;
  }

  /**
   * Allow a caller to pass in a CSS object so that it can be added to by this
   * parser.  This needs to happen before parsing, as this will replace the CSS
   * object already here.
   *
   * @param \Metrol\CSS $css The CSS object to replace the one already here
   *
   * @return this
   */
  public function setCSS(\Metrol\CSS $css)
  {
    $this->css = $css;

    return $this;
  }

  /**
   * Provide the CSS object stored here
   *
   * @return \Metrol\CSS
   */
  public function getCSS()
  {
    return $this->css;
  }
}
