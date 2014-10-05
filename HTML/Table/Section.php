<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table;

/**
 * Provide a base class for the Table Head, Body, and Foot.
 */
class Section
  extends \Metrol\HTML\Tag
  implements \Iterator
{
  /**
   * Table rows defined in the header area
   *
   * @var array of \Metrol\HTML\Table\Row objects
   */
  protected $rows;

  /**
   * The last Row object that was accessed through this object.
   *
   * @var \Metrol\HTML\Table\Row
   */
  protected $activeRow;

  /**
   * When requested, the striping object will be used to alternate the
   * background colors of the cells horizontally or vertically.
   *
   * @var \Metrol\HTML\Table\Effects\Striping
   */
  protected $stripingObj;

  /**
   */
  public function __construct($tagName)
  {
    parent::__construct($tagName, self::CLOSE_CONTENT);

    $this->rows = array();
  }

  /**
   * The output of this object
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * Adds a new cell to the table with contents
   *
   * @return \Metrol\HTML\Table\Row
   */
  public function addRow()
  {
    $row = new Row();

    $this->rows[] = $row;
    $this->activeRow = $row;

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
   * Adds a new Cell to the active row.
   * Will add a new row if one hasn't already been specified.
   *
   * @param string
   * @return \Metrol\HTML\Table\Cell
   */
  public function addCell($content)
  {
    if ( !is_object($this->activeRow) )
    {
      $this->addRow();
    }

    return $this->activeRow->addCell($content);
  }

  /**
   * Adds a new Header Cell to the active row.
   * Will add a new row if one hasn't already been specified.
   *
   * @param string
   * @return \Metrol\HTML\Table\HeaderCell
   */
  public function addHeaderCell($content)
  {
    if ( !is_object($this->activeRow) )
    {
      $this->addRow();
    }

    return $this->activeRow->addHeaderCell($content);
  }

  /**
   * Provide the striping effect object for this section
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function striping()
  {
    if ( !is_object($this->stripingObj) )
    {
      $this->stripingObj = new Effects\Striping($this);
    }

    return $this->stripingObj;
  }

  /**
   * Provide how many rows have been defined
   *
   * @return integer
   */
  public function getRowCount()
  {
    return count($this->rows);
  }

  /**
   * Walks through every row to look for the maximum number of cells to
   * determine the cell width of this section.
   *
   * @return integer
   */
  public function getSectionCellWidth()
  {
    $cellWidth = 0;

    foreach ( $this as $row )
    {
      $cellCount = $row->getCellCount();

      if ( $cellCount > $cellWidth )
      {
        $cellWidth = $cellCount;
      }
    }

    return $cellWidth;
  }

  /**
   * Get the contents of this class together for output.
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    // No rows, no output
    if ( count($this->rows) == 0 )
    {
      return $rtn;
    }

    if ( is_object($this->stripingObj) ) {
      $this->stripingObj->apply();
    }

    $this->setContent("\n");

    foreach ( $this as $row )
    {
      $this->addContent($row);
    }

    return parent::output()."\n";
  }

  /**
   * Implementing the Iterartor interface to walk through the rows
   */
  public function rewind()
  {
    reset($this->rows);
  }

  public function current()
  {
    return current($this->rows);
  }

  public function key()
  {
    return key($this->rows);
  }

  public function next()
  {
    return next($this->rows);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
