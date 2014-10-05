<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Base class for all HTML tags defined by Metrol classes
 */
abstract class Tag
{
  /**
   * Closure type defined to have a closing tag at the end of some content.
   *
   * @const
   */
  const CLOSE_CONTENT = 0;

  /**
   * Closure type defined for tags that close within themselves.
   *
   * @const
   */
  const CLOSE_SELF = 1;

  /**
   * Closure type defined to not close the tag automatically.
   *
   * @const
   */
  const CLOSE_NONE = 2;

  /**
   * The name of the tag
   *
   * @var string
   */
  private $tagName;

  /**
   * List of tag attributes used in the anchor
   *
   * @var \Metrol\HTML\Tag\Attribute
   */
  protected $attribObj;

  /**
   * For tags that will contain information within them, this will be the var
   * where that is stored.
   *
   * @var string
   */
  private $contentVal;

  /**
   * Which kind of tag closure this tag needs
   *
   * @var integer
   */
  private $closure;

  /**
   * The URL object that can be utilized by any class that needs to access
   * a file or network resource via a URL.
   *
   * @var \Metrol\URL
   */
  private $urlObj;

  /**
   * Text that will appear before the opening tag.
   *
   * @var string
   */
  private $prefix;

  /**
   * Text that appears after the closing tag
   *
   * @var string
   */
  private $suffix;

  /**
   * Stores the name of the tag to later be used to assemble output from here.
   *
   * @param string
   * @param integer What kind of closure this tag requires
   */
  public function __construct($tagName, $closure)
  {
    $this->attribObj  = new Tag\Attribute();
    $this->contentVal = '';
    $this->prefix     = '';
    $this->suffix     = '';
    $this->closure    = self::CLOSE_CONTENT;

    $this->setTagName($tagName);
    $this->setClosureType($closure);
  }

  /**
   * Provide the assembled tag
   *
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * Assemble the tag
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    if ( strlen($this->prefix) > 0 )
    {
      $rtn .= $this->prefix;
    }

    $rtn .= $this->open();

    if ( $this->closure == self::CLOSE_CONTENT )
    {
      $rtn .= $this->contentVal;
    }

    if ( $this->closure == self::CLOSE_NONE )
    {
      $rtn .= $this->contentVal;
    }

    if ( $this->closure == self::CLOSE_CONTENT )
    {
      $rtn .= $this->close();

      if ( strlen($this->suffix) > 0 )
      {
        $rtn .= $this->suffix;
      }
    }

    if ( $this->closure == self::CLOSE_SELF )
    {
      if ( strlen($this->suffix) > 0 )
      {
        $rtn .= $this->suffix;
      }
    }

    return $rtn;
  }

  /**
   * Make sure any URL objects are also cloned going into the new tag object
   */
  public function __clone()
  {
    if ( is_object($this->urlObj) )
    {
      $u = clone $this->urlObj;
      $this->urlObj = null;
      $this->urlObj = $u;
    }

    $this->attribObj = clone $this->attribObj;
  }

  /**
   * Sets what tag name this is for.
   *
   * @param string
   * @return this
   */
  protected function setTagName($tag)
  {
    $tag = substr(strtolower($tag), 0, 50);

    $this->tagName = $tag;

    return $this;
  }

  /**
   * Allow the closure type for this tag be changed.
   *
   * @param integer
   * @return this
   */
  public function setClosureType($closure)
  {
    $closure = intval($closure);

    switch ($closure)
    {
      case self::CLOSE_CONTENT:
        $this->closure = $closure;
        break;

      case self::CLOSE_SELF:
        $this->closure = $closure;
        break;

      case self::CLOSE_NONE:
        $this->closure = $closure;
        break;

      case self::CLOSE_CONTENT:
        $this->closure = $closure;
        break;

      default:
        $this->closure = self::CLOSE_CONTENT;
    }

    return $this;
  }

  /**
   * Sets a bit of text that will show up before the opening tag when ouput
   *
   * @param string
   * @return this
   */
  public function setPrefix($text)
  {
    $this->prefix = $text;

    return $this;
  }

  /**
   * Sets a bit of text that will show just after the closing tag when output
   *
   * @param string
   * @return this
   */
  public function setSuffix($text)
  {
    $this->suffix = $text;

    return $this;
  }

  /**
   * Provide only the opening for the tag in question.
   *
   * @return string
   */
  public function open()
  {
    $rtn = '<';
    $rtn .= $this->tagName;

    $rtn .= $this->attribute();

    if ( $this->closure == self::CLOSE_SELF )
    {
      $rtn .= ' />';
    }
    else
    {
      $rtn .= '>';
    }

    return $rtn;
  }

  /**
   * Provide only the closure for the tag.
   *
   * @return string
   */
  public function close()
  {
    if ( $this->closure == self::CLOSE_SELF )
    {
      return '';
    }

    $rtn = '</'.$this->tagName.'>';

    return $rtn;
  }

  /**
   * Provide the attribute object attached to this tag
   *
   * @return \Metrol\HTML\Tag\Attribute
   */
  public function attribute()
  {
    return $this->attribObj;
  }

  /**
   * Sets the contents of this tag.
   *
   * @param string
   * @return this
   */
  public function setContent($val)
  {
    $this->contentVal = $val;

    return $this;
  }

  /**
   * Sets the contents of this tag after being run through htmlentities.
   *
   * @param string
   * @return this
   */
  public function setHTMLContent($string)
  {
    $this->contentVal = \htmlentities($string);

    return $this;
  }

  /**
   * Adds content to whatever is already in the contents.
   * This does not provide line feeds.
   *
   * @param string
   * @return this
   */
  public function addContent($val)
  {
    $this->contentVal .= $val;

    return $this;
  }

  /**
   * Provides whatever is stored in the contentsVal
   *
   * @return string
   */
  public function getContent()
  {
    return $this->contentVal;
  }

  /**
   * Provides the URL object that this tag can utilize for one of its
   * attributes.
   *
   * @return \Metrol\URL
   */
  public function url()
  {
    if ( !is_object($this->urlObj) )
    {
      $this->urlObj = new \Metrol\URL();
    }

    return $this->urlObj;
  }

  /**
   * Sets the URL object with the string passed in.
   *
   * @param string
   * @return this
   */
  public function setURL($urlString)
  {
    $this->urlObj = new \Metrol\URL($urlString);

    return $this;
  }

  /**
   * Set the URL using a Metrol_URL object.
   *
   * @param \Metrol\URL
   * @return this
   */
  public function setURLObj(\Metrol\URL $url)
  {
    $this->urlObj = $url;

    return $this;
  }

  /**
   * Sets the name attribute for this tag
   *
   * @param string
   * @return this
   */
  public function setName($nameValue)
  {
    $this->attribute()->name = $nameValue;

    return $this;
  }

  /**
   * Sets the title attribute for this tag
   *
   * @param string
   * @return this
   */
  public function setTitle($titleName)
  {
    if ( strlen($titleName) > 0 )
    {
      $this->attribute()->title = $titleName;
    }
    else
    {
      $this->attribute()->delete('title');
    }

    return $this;
  }

  /**
   * Adds a JavaScript event to the tag
   *
   * @param string Javascript to run
   * @param string What kind of event to bind to.
   * @return this
   */
  public function setEvent($jsCall, $eventType = "onClick")
  {
    $this->attribute()->$eventType = $jsCall;

    return $this;
  }

  /**
   * Sets the CSS Class name for this tag
   *
   * @param string
   * @return this
   */
  public function setClass($className)
  {
    $this->attribute()->class = $className;

    return $this;
  }

  /**
   * Sets the CSS ID for this tag
   *
   * @param string
   * @return this
   */
  public function setID($idName)
  {
    $this->attribute()->add("id", $idName);

    return $this;
  }

  /**
   * Adds an inline style rule to this tag
   *
   * @param string Style attribute
   * @param string Value for attribute
   * @return this
   */
  public function addStyle($style, $value)
  {
    $this->attribute()->addStyle($style, $value);

    return $this;
  }

  /**
   * Same exact thing as addStyle()
   * Only added here since I seem to want to call that instead of add.
   *
   * @param string Style attribute
   * @param string Value for attribute
   * @return this
   */
  public function setStyle($style, $value)
  {
    return $this->addStyle($style, $value);
  }

  /**
   * Sets the width attribute of the tag
   *
   * @param integer
   * @return this
   */
  public function setWidth($val)
  {
    if ( strpos($val, '%') )
    {
      $val = intval($val).'%';
    }
    else
    {
      $val = intval($val);
    }

    $this->attribute()->width = $val;

    return $this;
  }

  /**
   * Sets the height attribute of the tag
   *
   * @param integer
   * @return this
   */
  public function setHeight($val)
  {
    if ( strpos($val, '%') )
    {
      $val = intval($val).'%';
    }
    else
    {
      $val = intval($val);
    }

    $this->attribute()->height = $val;

    return $this;
  }
}
