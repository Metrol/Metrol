<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Assembles an unordered list used for displaying links as tabs
 */
class Tabs
{
  /**
   * Index value to be passed along with the linked URL
   *
   * @var integer
   */
  protected $id;

  /**
   * Defines the name of the index parameter for the URL.
   *
   * @var string
   */
  protected $idName;

  /**
   * Note which tab is presently selected
   *
   * @var string
   */
  protected $activeTab;

  /**
   * Which tab is actively being worked on.  Usually the last added tab.
   * The tab name is what is defined here.
   *
   * @var string
   */
  protected $tabInWork;

  /**
   * Defines what tabs will show up, and what they will link to
   * Format: $tabs["Tab Name"] = "link.php"
   * An index value will automatically be added.
   *
   * @var array
   */
  protected $tabs;

  /**
   * Initializes the Tab object
   */
  public function __construct()
  {
    $this->id        = 0;
    $this->idName    = 'id';
    $this->activeTab = '';
    $this->tabInWork = '';
    $this->tabs      = array();
  }

  /**
   * Provide the HTML output of this class
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = "";

    $rtn .= "<div id=\"tabs\">\n";
    $rtn .= "<ul>\n";

    $rtn .= $this->getTabs();

    $rtn .= "</ul>\n";
    $rtn .= "</div>\n";
    $rtn .= "<br style=\"clear: both;\" />\n\n";

    return $rtn;
  }

  /**
   * Set the value of the ID key in the URL
   *
   * @param integer
   * @return this
   */
  public function setID($id)
  {
    $this->id = intval($id);

    return $this;
  }

  /**
   * Set the name of the index field paramter.
   *
   * @param string
   * @return this
   */
  public function setIndexName($indexName)
  {
    $this->idName = $indexName;

    return $this;
  }

  /**
   * Adds new tabs to the list that will be displayed.
   * When the index value is greater than zero it will automatically be
   * appended to the URL.
   *
   * @param string Name that will show up on the tab
   * @param string URL the tab will link to
   * @param string Text to show up for the title attribute
   *
   * @return this
   */
  public function addTab($name, $url, $toolTip = null)
  {
    $dispName = "<span>";
    $dispName .= \Metrol\Text::htmlent($name);
    $dispName .= "</span>";

    $a = new Anchor($url, $dispName);

    if ( $this->id > 0 )
    {
      $a->param($this->idName, $this->id);
    }

    if ( $toolTip !== null )
    {
      $a->setTitle($toolTip);
    }

    $this->tabs[$name] = $a;
    $this->tabInWork = $name;

    return $this;
  }

  /**
   * Takes in an Anchor object and adds it as a tab to the list.
   *
   * @param Metrol\HTML\Anchor
   * @return this
   */
  public function addAnchorTab(Anchor $a)
  {
    $ac = clone $a;

    $name = $ac->contentsText();

    $ac->linkText("<span>$name</span>");

    $this->tabs[$name] = $ac;
    $this->tabInWork = $name;

    return $this;
  }

  /**
   * Adds new tabs to the list that will be displayed with links that are
   * Javascript functions.
   * When the index value is greater than zero it will automatically be
   * appended to the URL.
   *
   * @param string Name that will show up on the tab
   * @param string Javascript function to be called on click
   * @return this
   */
  public function addJSTab($name, $jsFunction)
  {
    $dispName = "<span>";
    $dispName .= \Metrol\Text::htmlent($name);
    $dispName .= "</span>";

    $a = new Anchor;
    $a->linkText($dispName);
    $a->setJS($jsFunction);
    $a->title($name);

    $this->tabs[$name] = $a;
    $this->tabInWork = $name;

    return $this;
  }

  /**
   * Adds an additional parameter to the link of the specified tab.
   *
   * @param string
   * @param mixed
   * @param string Which tab to affect
   * @return this
   */
  public function addParam($key, $val, $tab = "YYXXZZ")
  {
    if ( $tab == "YYXXZZ" ) {
      $tab = $this->tabInWork;
    }

    if ( !array_key_exists($tab, $this->tabs) )
    {
      return; // Couldn't find the tab requested
    }

    $this->tabs[$tab]->param($key, $val);

    return $this;
  }

  /**
   * Adds a parameter to all of the tabs already set.
   * Does not impact any new tabs added after this is called.
   *
   * @param string
   * @param mixed
   * @return this
   */
  public function addParamToAll($key, $val)
  {
    foreach ( $this->tabs as $tab )
    {
      $tab->param($key, $val);
    }

    return $this;
  }

  /**
   * Which tab is presently active
   *
   * @param string
   * @return this
   */
  public function setActiveTab($tabName)
  {
    $this->activeTab = $tabName;

    return $this;
  }

  /**
   * Assembles all tabs and their links for the toString method
   *
   * @return string
   */
  private function getTabs()
  {
    $rtn = "";

    foreach ($this->tabs as $name => $link)
    {
      $style = "";

      if ( strtoupper($name) == strtoupper($this->activeTab) )
      {
        $style = " id=\"current\"";
      }

      $rtn .= "  <li".$style.">\n";
      $rtn .= "    $link\n";
      $rtn .= "  </li>\n";
    }

    return $rtn;
  }
}
