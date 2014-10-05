<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Foot;
use Metrol\HTML as html;

/**
 * Everything that will appear at the bottom of an HTML document.
 *
 */
class Area
{
  /**
   * Stack of whatever kind of stuff a caller might want slapped at the bottom
   * of a page.
   *
   * @var array
   */
  private $footStack;

  /**
   * When set, the closure of the footer area will close both the HTML and BODY
   * tags.
   *
   * @var boolean
   */
  private $closeBody;

  /**
   * Instantiat the object
   *
   */
  public function __construct()
  {
    $this->footStack = array();
    $this->closeBody = false;
  }

  /**
   * Produces an output footer area from the information passed in up to the
   * point of being called.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->buildArea();
  }

  /**
   * Use to toggle the flag that determines if the body tag needs to be closed
   * from here
   *
   * @param boolean
   * @return this
   */
  public function setBodyClose($flag)
  {
    if ( $flag )
    {
      $this->closeBody = true;
    }
    else
    {
      $this->closeBody = false;
    }

    return $this;
  }

  /**
   * Sets the content to appear in the footer, replacing anything that was
   * previously here.
   *
   * @param string
   * @return this
   */
  public function setContent($content)
  {
    $this->clearContent();
    $this->addContent($content);

    return $this;
  }

  /**
   * Used to add in free form text to the footer area.  Use with caution.
   *
   * @param string
   * @return this
   */
  public function addContent($content)
  {
    $this->footStack[] = $content;

    return $this;
  }

  /**
   * Clears out all the content from the footer, all ready to start fresh!
   *
   * @return this
   */
  public function clearContent()
  {
    $this->footStack = array();

    return $this;
  }

  /**
   * Assembles all the parts for the footer area.
   *
   * @return string
   */
  protected function buildArea()
  {
    $rtn = '';

    $stacks = array($this->footStack);

    foreach ( $stacks as $stack )
    {
      foreach ( $stack as $content )
      {
        $rtn .= '  '.$content."\n";
      }
    }

    if ( $this->closeBody )
    {
      $rtn .= "\n</body>";
    }

    $rtn .= "\n</html>";

    return $rtn;
  }
}
