<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Head;
use Metrol\HTML as html;

/**
 * Contains all the information for an HTML header area.
 */
class Area
{
  /**
   * What kind of document type are we going to use.
   * This is defined in Metrol\HTML\DocType
   *
   * @var integer
   */
  private $documentType;

  /**
   * What language this page should render for.
   *
   * @var string
   */
  private $pageLanguage;

  /**
   * The title of the page
   *
   * @var \Metrol\HTML\Title
   */
  private $pageTitle;

  /**
   * Meta tags that have been added to this area
   *
   * @var array
   */
  private $metaStack;

  /**
   * Javascript files to be included
   *
   * @var array
   */
  private $scriptStack;

  /**
   * Link tags to be included
   *
   * @var array
   */
  private $linkStack;

  /**
   * Style sheets to be included
   *
   * @var array
   */
  private $styleStack;

  /**
   * Free form set of strings that can be passed in to show up within the
   * head area.
   *
   * @var array
   */
  private $textStack;

  /**
   * Copyright notice to be included with the source HTML
   *
   * @var string
   */
  private $copyrightNotice;

  /**
   * Initialize the defaults for this object
   *
   */
  public function __construct()
  {
    $this->metaStack   = array();
    $this->scriptStack = array();
    $this->linkStack   = array();
    $this->styleStack  = array();
    $this->textStack   = array();

    $this->pageLanguage = 'en';
    $this->documentType = html\DocType::HTML_V5;
    $this->copyrightNotice = '';
  }

  /**
   * Produces an output head area from the information passed in up to the
   * point of being called.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->buildArea();
  }

  /**
   * Provide the ability to pull out certain parts of the head area as though
   * they were member vars
   *
   * @param string Variable name
   * @return mixed
   */
  public function __get($var)
  {
    switch ( $var )
    {
      case 'title':
        $rtn = $this->pageTitle->getContent();
        break;

      default:
        $rtn = null;
        break;
    }

    return $rtn;
  }

  /**
   * Provide the ability to set certain parts of the head area as though
   * they were member vars
   *
   * @param string Variable name
   * @param mixed Value of that variable
   */
  public function __set($var, $value)
  {
    switch ( strtolower($var) )
    {
      case 'title':
        $this->setTitle($value);
        break;

      case 'jsfile':
        $this->addJsFile($value);
        break;

      case 'jscode':
        $this->addJsCode($value);
        break;

      case 'style':
        $this->addStyle($value);
        break;

      case 'stylesheet':
        $this->addStyleSheet($value);

      default:
        break;
    }
  }

  /**
   * Make sure that templates can recognize the magic set/get values
   *
   * @param string Variable name
   * @return boolean
   */
  public function __isset($name)
  {
    switch ( $name )
    {
      case 'title':
        $rtn = true;
        break;

      default:
        $rtn = false;
        break;
    }

    return $rtn;
  }

  /**
   * Changes the page title
   *
   * @param string The page title
   * @return this
   */
  public function setTitle($title)
  {
    $this->pageTitle = new html\Title($title);

    return $this;
  }

  /**
   * Changes the document type reported at the very top of the page
   *
   * @param integer
   * @return this
   */
  public function setDocType($docType)
  {
    $this->documentType = intval($docType);

    return $this;
  }

  /**
   * Sets the language type to be used.
   *
   * @param string Language code
   * @return this
   */
  public function setLanguage($language)
  {
    $this->pageLanguage = $language;
  }

  /**
   * Sets the character encoding for the page
   *
   * @param string Character Set
   * @return this
   */
  public function setCharacterEncoding($charSet)
  {
    $meta = new html\Meta;

    if ( $this->documentType == html\DocType::HTML_V5)
    {
      $meta->setCharSet($charSet);
    }
    else
    {
      $meta->setCharacterEncoding($charSet);
    }

    $this->metaStack[] = $meta;

    return $this;
  }

  /**
   * Turns on or off the robots flag.  Turning it off tells search engines
   * not to index this page.
   *
   * @param bool
   * @return this
   */
  public function setRobotIndex($flag = TRUE)
  {
    $this->clearMetaName('robots');

    $meta = new html\Meta;

    if ( $flag )
    {
      $meta->setRobotIndex(true);
    }
    else
    {
      $meta->setRobotIndex(false);
    }

    $this->addMetaTag($meta);

    return $this;
  }

  /**
   * Used to add in free form text to the header area.  Use with caution.
   *
   * @param string
   * @return this
   */
  public function addText($text)
  {
    $this->textStack[] = $text;

    return $this;
  }

  /**
   * Sets the shortcut icon for this page.  Should show up next to bookmarks
   * for the site.
   *
   * @param string Filename of the icon
   * @return this
   */
  public function setFavoriteIcon($file)
  {
    $link = new html\Link;
    $link->setHref($file)
         ->setRel('icon')
         ->setType('image/ico');

    $this->addLinkTag($link);

    return $this;
  }

  /**
   * Giving credit where it's due
   *
   * @param string Name of the author
   * @return this
   */
  public function addAuthor($author)
  {
    $meta = new html\Meta;
    $meta->setAuthor($author);

    $this->addMetaTag($meta);

    return $this;
  }

  /**
   * Set the Meta Description for this page
   *
   * @param string
   * @return this
   */
  public function setDescription($description)
  {
    $this->clearMetaName('description');

    $meta = new html\Meta;

    $meta->setName('description');
    $meta->setContent( \Metrol\Text::htmlent($description) );

    $this->addMetaTag($meta);

    return $this;
  }

  /**
   * Adds meta keywords to the page.
   * This method can take any number of string arguments, or arrays of strings
   * to add to the keyword list.
   *
   * @param string|array
   * @return this
   */
  public function setKeywords()
  {
    $this->clearMetaName('keywords');

    $keyWords = array();
    $arguments = func_get_args();

    // Walk through the arguments coming in
    foreach ($arguments as $arg)
    {
      // Arguments that are arrays need to get walked through as well
      if ( is_array($arg) )
      {
        foreach ( $arg as $subArg )
        {
          $keyWords[] = strval($subArg);
        }
      }
      else
      {
        $keyWords[] = strval($arg);
      }
    }

    if ( count($keyWords) > 0 )
    {
      $meta = new html\Meta;
      $meta->setKeyWords($keyWords);

      $this->addMetaTag($meta);
    }

    return $this;
  }

  /**
   * Sets a copyright notice that will appear as an HTML comment just below
   * the closing head tag.
   * The text is automatically wrapped, so there is no need to pass in line
   * feeds.
   *
   * @param string Copyright text
   * @return this
   */
  public function setCopyrightText($text)
  {
    $this->copyrightNotice = $text;

    return $this;
  }

  /**
   * Sets the copyright date that will show up as a meta tag in the source
   *
   * @param string Year of the copyright
   * @return this
   */
  public function setCopyrightDate($cpDate)
  {
    $this->clearMetaName('copyright');

    $meta = new html\Meta;
    $meta->setCopyright($cpDate);

    $this->addMetaTag($meta);

    return $this;
  }

  /**
   * Adds a javascript file include to the header.
   * Don't worry about adding the same file in multiple times.  This method is
   * smart enough to avoid putting duplicates.
   *
   * @param string File name
   * @return this
   */
  public function addJsFile($file)
  {
    $script = new html\Script();
    $script->setURL($file)
           ->setType('text/javascript');

    if ( !in_array($script, $this->scriptStack) )
    {
      $this->addScriptTag($script);
    }

    return $this;
  }

  /**
   * Inserts javascript code in-line rather than including a file.
   *
   * @param string JS Code to insert
   * @return this
   */
  public function addJsCode($jsCode)
  {
    $script = new html\Script();
    $script->setType('text/javascript')
           ->setContent("\n".$jsCode."\n  ");

    $this->addScriptTag($script);

    return $this;
  }

  /**
   * Puts the specified styles in-line to the header area
   *
   * @param string Raw style information
   * @param string What kind of media this style sheet is for
   * @return this
   */
  public function addStyle($styles, $media = "screen")
  {
    $media = strtolower($media);

    $style = new html\Style;
    $style->setMedia($media)
          ->setContent("\n".$styles."\n");

    $this->addStyleTag($style);

    return $this;
  }

  /**
   * Adds a style sheet include to the stack
   *
   * @param string File name
   * @param string What kind of media this style sheet is for
   * @return this
   */
  public function addStyleSheet($file, $media = "screen")
  {
    $media = strtolower($media);

    $link = new html\Link;
    $link->setHref($file)
         ->setMedia($media)
         ->setRel('stylesheet')
         ->setType('text/css');

    $this->addLinkTag($link);

    return $this;
  }

  /**
   * Removes all the included style sheets from the header area
   *
   * @return this
   */
  public function clearStyleSheets()
  {
    $toDelete = array();

    foreach ( $this->linkStack as $idx => $linkTag )
    {
      if ( $linkTag->attribute()->rel == 'stylesheet' )
      {
        $toDelete[] = $idx;
      }
    }

    foreach ( $toDelete as $idx )
    {
      unset($this->linkStack[$idx]);
    }

    return $this;
  }

  /**
   * Provide a means to toggle the Internet Explorer V7 compatibility mode.
   *
   * @param boolean
   * @return this
   */
  public function setIE7Compat()
  {
    $meta = new html\Meta;
    $meta->ie7Compat();

    $this->addMetaTag($meta);

    return $this;
  }

  /**
   * Clear out a Meta tag from the stack based on the name.
   * This is to insure that we don't have multiples of certain meta tags.
   *
   * @param string
   */
   private function clearMetaName($metaName)
   {
     $found = null;

     foreach ( $this->metaStack as $idx => $meta )
     {
       if ( $meta->attribute()->name == $metaName )
       {
         $found = $idx;
         break;
       }
     }

     if ( $found !== null )
     {
       unset($this->metaStack[$found]);
     }
   }

  /**
   * Adds any script tag object to the stack to be included in the header
   *
   * @param \Metrol\HTML\Script
   * @return this
   */
  public function addScriptTag(html\Script $script)
  {
    $this->scriptStack[] = $script;

    return $this;
  }

  /**
   * Adds any Link tag object to the stack to be included in the header
   *
   * @param \Metrol\HTML\Link
   * @return this
   */
  public function addLinkTag(html\Link $link)
  {
    $this->linkStack[] = $link;

    return $this;
  }

  /**
   * Adds any Meta tag object to the stack to be included in the header
   *
   * @param \Metrol\HTML\Meta
   * @return this
   */
  public function addMetaTag(html\Meta $meta)
  {
    $this->metaStack[] = $meta;

    return $this;
  }

  /**
   * Adds any Style tag object to the stack to be included in the header
   *
   * @param \Metrol\HTML\Style
   * @return this
   */
  public function addStyleTag(html\Style $style)
  {
    $this->styleStack[] = $style;

    return $this;
  }

  /**
   * Assembles all the parts for the head area.
   *
   * @return string
   */
  protected function buildArea()
  {
    $rtn = '';

    $docType = new html\DocType($this->documentType, $this->pageLanguage);
    $head = new html\Head;
    $html = new html\Html;
    $html->setDocType($docType)->setLanguage($this->pageLanguage);

    $rtn .= $docType."\n";
    $rtn .= $html."\n";
    $rtn .= $head."\n";

    if ( is_object($this->pageTitle) )
    {
      $rtn .= '  '.$this->pageTitle."\n";
    }

    $stacks = array($this->metaStack, $this->linkStack, $this->styleStack,
                    $this->scriptStack, $this->textStack);

    foreach ( $stacks as $stack )
    {
      foreach ( $stack as $tag )
      {
        $rtn .= '  '.$tag."\n";
      }
    }

    $rtn .= $head->close()."\n";

    if ( strlen($this->copyrightNotice) > 0 )
    {
      $rtn .= "\n<!-- COPYRIGHT NOTICE\n";
      $rtn .= wordwrap($this->copyrightNotice, 80);
      $rtn .= "\n-->\n\n";
    }

    return $rtn;
  }
}
