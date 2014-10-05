<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table;

/**
 * Defines an HTML Table Row
 */
class Row
  extends \Metrol\HTML\Tag
  implements \Iterator
{
  /**
   * Table cells defined on this table row
   *
   * @var array of \Metrol\HTML\Table\Cell objects
   */
  private $cells;

  /**
   * Keep track of the last cell that had activity through this object
   *
   * @var \Metrol\HTML\Table\Cell
   */
  private $activeCell;

  /**
   */
  public function __construct()
  {
    parent::__construct('tr', self::CLOSE_CONTENT);

    $this->cells = array();
  }

  public function __toString()
  {
    return $this->output();
  }

  /**
   * Adds a new cell to the table with contents
   *
   * @param string
   * @return \Metrol\HTML\Table\Cell
   */
  public function addCell($content)
  {
    $cell = new Cell();
    $cell->setContent($content);

    $this->cells[] = $cell;
    $this->activeCell = $cell;

    return $cell;
  }

  /**
   * Adds a new header cell to the row
   *
   * @param string
   * @return \Metrol\HTML\Table\HeaderCell
   */
  public function addHeaderCell($content)
  {
    $cell = new HeaderCell();
    $cell->setContent($content);

    $this->cells[] = $cell;
    $this->activeCell = $cell;

    return $cell;
  }

  /**
   * Applies a style to all the cells in the row.
   * This will not impact cells that have not yet been added to the row.
   *
   * @param string
   * @param string
   * @return \Metrol\HTML\Table\Row
   */
  public function styleCells($style, $value)
  {
    foreach ( $this as $cell )
    {
      $cell->addStyle($style, $value);
    }
  }

  /**
   * Provide the active cell
   *
   * @return \Metrol\HTML\Table\Cell
   */
  public function getActiveCell()
  {
    return $this->activeCell;
  }

  /**
   * Provide a cell by the index value in the row
   *
   * @param integer
   * @return \Metrol\HTML\Table\Cell
   */
  public function getCell($index)
  {
    $index = intval($index);

    if ( array_key_exists($index, $this->cells) )
    {
      return $this->cells[$index];
    }
  }

  /**
   * Specify how many cells are in this row
   *
   * @return integer
   */
  public function getCellCount()
  {
    return count($this->cells);
  }

  /**
   * Get the contents of this class together for output.
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    // No cells, no output
    if ( count($this->cells) == 0 )
    {
      return $rtn;
    }

    $rtn .= '  '.$this->open();
    $this->setContent("\n");

    foreach ( $this as $cell )
    {
      $this->addContent('    '.$cell."\n");
    }

    $rtn .= $this->getContent();

    $rtn .= '  '.$this->close()."\n";

    return $rtn;
  }

  /**
   * Implementing the Iterartor interface to walk through the cells
   */
  public function rewind()
  {
    reset($this->cells);
  }

  public function current()
  {
    return current($this->cells);
  }

  public function key()
  {
    return key($this->cells);
  }

  public function next()
  {
    return next($this->cells);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
