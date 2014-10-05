<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML;

/**
 * Define the Head tag
 */
class Table extends Tag
{
  /**
   * The caption of this table
   *
   * @var \Metrol\HTML\Table\Caption
   */
  private $caption;

  /**
   * The head area of the table
   *
   * @var \Metrol\HTML\Table\Head
   */
  private $head;

  /**
   * List of Bodies used for this table
   *
   * @var array of \Metrol\HTML\Table\Body objects
   */
  private $bodies;

  /**
   * The Foot area of the table
   *
   * @var \Metrol\HTML\Table\Foot
   */
  private $foot;

  /**
   * Specify which table section is presently active.
   * This can be the table head, body, or foot
   *
   * @var \Metrol\HTML\Table\Section
   */
  private $activeSection;

  /**
   * Text to appear above the opening table tag.
   *
   * @var array
   */
  private $before;

  /**
   * Text to appear immediately after the closing table tag.
   *
   * @var array
   */
  private $after;

  /**
   * @param string
   */
  public function __construct($tableTitle = '')
  {
    parent::__construct('table', self::CLOSE_CONTENT);

    $this->setTitle($tableTitle);

    $this->after  = array();
    $this->before = array();

    $this->initAreas();
  }

  /**
   * Override the parent class to specify calling to the output() method to
   * assemble this tag.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * Adds text to a stack of content that will appear just before the table's
   * opening tag.
   *
   * @param string
   * @return \Metrol\HTML\Table
   */
  public function setBefore($text)
  {
    $this->before[] = $text;

    return $this;
  }

  /**
   * Adds text to a stack of content that will appear just before the table's
   * opening tag.
   *
   * @param string
   * @return \Metrol\HTML\Table
   */
  public function setAfter($text)
  {
    $this->after[] = $text;

    return $this;
  }

  /**
   * Sets the value for the caption
   *
   * @param string
   * @return \Metrol\HTML\Table
   */
  public function setCaption($captionText)
  {
    $this->caption->setContent($captionText);

    return $this;
  }

  /**
   * Provide the caption object in use for the table
   *
   * @return \Metrol\HTML\Table\Caption
   */
  public function getCaption()
  {
    return $this->caption;
  }

  /**
   * Sets which Table Body will be actively written to.
   * An value not already defined will create a new table body.
   *
   * @param integer
   * @return \Metrol\HTML\Table
   */
  public function setBodyActive($bodyIndex = 0)
  {
    $bodyIndex = intval($bodyIndex);

    if ( array_key_exists($bodyIndex, $this->bodies) )
    {
      $this->activeSection = $this->bodies[$bodyIndex];
    }
    else
    {
      $this->activeSection = new Table\Body();
      $this->bodies[] = $this->activeSection;
    }

    return $this;
  }

  /**
   * Sets the Table Header object as the section being written to.
   *
   * @return \Metrol\HTML\Table
   */
  public function setHeadActive()
  {
    $this->activeSection = $this->head;

    return $this;
  }

  /**
   * Sets the Table Footer object as the section being written to.
   *
   * @return \Metrol\HTML\Table
   */
  public function setFootActive()
  {
    $this->activeSection = $this->foot;

    return $this;
  }

  /**
   * Provide the Active Section object
   *
   * @return \Metrol\HTML\Table\Section
   */
  public function getActiveSection()
  {
    return $this->activeSection;
  }

  /**
   * Adds a new row to the active body area.
   * You can pass in a list of arguments that will be added as cells.
   * Any arrays in those arguments will create cells as well.
   *
   * @param mixed
   * @return \Metrol\HTML\Table\Row
   */
  public function addRow()
  {
    $row = $this->activeSection->addRow();

    if ( func_num_args() > 0 )
    {
      $args = func_get_args();

      foreach ( $args as $val )
      {
        if ( is_array($val) )
        {
          foreach ( $val as $arVal )
          {
            $row->addCell($arVal);
          }
        }
        else
        {
          $row->addCell($val);
        }
      }
    }

    return $row;
  }

  /**
   * Add a new cell to the active body's active row
   *
   * @return \Metrol\HTML\Table\Cell
   */
  public function addCell($content)
  {
    return $this->activeSection->addCell($content);
  }

  /**
   * Add a new cell to the active body's active row
   *
   * @return \Metrol\HTML\Table\Cell
   */
  public function addHeaderCell($content)
  {
    return $this->activeSection->addHeaderCell($content);
  }

  /**
   * Set the border attribute for the table
   *
   * @param integer
   * @return \Metrol\HTML\Table
   */
  public function setBorder($size)
  {
    $this->attribute()->border = intval($size);

    return $this;
  }

  /**
   * Set the cell padding attribute for the table
   *
   * @param integer
   * @return \Metrol\HTML\Table
   */
  public function setPadding($size)
  {
    $this->attribute()->cellpadding = intval($size);

    return $this;
  }

  /**
   * Set the cell spacing attribute for the table
   *
   * @param integer
   * @return \Metrol\HTML\Table
   */
  public function setSpacing($size)
  {
    $this->attribute()->cellspacing = intval($size);

    return $this;
  }

  /**
   * Get the striping object for the active table section.
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function striping()
  {
    return $this->activeSection->striping();
  }

  /**
   * Sets up the basic parts of this table
   */
  public function initAreas()
  {
    $this->caption = new Table\Caption();
    $this->head = new Table\Head();
    $this->foot = new Table\Foot();

    $this->bodies = array();
    $this->bodies[0] = new Table\Body();
    $this->activeSection = $this->bodies[0];
  }

  /**
   * Produce the output from this tag and all of it's components.
   *
   * @return string
   */
  public function output()
  {
    $this->setContent("\n");

    $this->addContent($this->caption);
    $this->addContent($this->head);

    foreach ( $this->bodies as $body)
    {
      $this->addContent($body);
    }

    $this->addContent($this->foot);

    $rtn = parent::output()."\n";

    $beforeText = '';
    $afterText  = '';

    foreach ( $this->before as $text )
    {
      $beforeText .= $text."\n";
    }

    foreach ( $this->after as $text )
    {
      $afterText .= $text."\n";
    }

    $rtn = $beforeText.$rtn.$afterText;

    return $rtn;
  }
}
