<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table;

/**
 * Defines a cell in an HTML table
 */
class Cell extends \Metrol\HTML\Tag
{
  /**
   */
  public function __construct()
  {
    parent::__construct('td', self::CLOSE_CONTENT);
  }

  /**
   * Sets the number of columns this cell will span across.
   *
   * @param integer
   * @return \Metrol\HTML\Table\Cell
   */
  public function setColSpan($columns)
  {
    $columns = abs(intval($columns));

    if ( $columns == 1 )
    {
      $this->attribute()->delete('colspan');

      return $this;
    }

    $this->attribute()->colspan = $columns;

    return $this;
  }

  /**
   * Sets the number of rows this cell will span across.
   *
   * @param integer
   * @return \Metrol\HTML\Table\Cell
   */
  public function setRowSpan($rows)
  {
    $rows = abs(intval($rows));

    if ( $rows == 1 )
    {
      $this->attribute()->delete('rowspan');

      return $this;
    }

    $this->attribute()->rowspan = $rows;

    return $this;
  }

  /**
   * Sets the horizontal alignment of the cell contents
   *
   * @param string
   * @return \Metrol\HTML\Table\Cell
   */
  public function setAlign($direction)
  {
    switch (strtolower($direction))
    {
      case 'left':
        $this->addStyle('text-align', 'left');
        break;

      case 'center':
        $this->addStyle('text-align', 'center');
        break;

      case 'right':
        $this->addStyle('text-align', 'right');
        break;

      default:
        $this->attribute()->deleteStyle('text-align');
    }

    return $this;
  }

  /**
   * Sets the vertical alignment of the cell contents
   *
   * @param string
   * @return \Metrol\HTML\Table\Cell
   */
  public function setVerticalAlign($direction)
  {
    switch (strtolower($direction))
    {
      case 'top':
        $this->attribute()->valign = 'top';
        break;

      case 'middle':
        $this->attribute()->valign = 'middle';
        break;

      case 'bottom':
        $this->attribute()->valign = 'bottom';
        break;

      case 'baseline':
        $this->attribute()->valign = 'baseline';
        break;

      default:
        $this->attribute()->delete('valign');
    }

    return $this;
  }

  /**
   * Eanble/Disable the No Wrap attribute
   *
   * @param boolean
   * @return \Metrol\HTML\Table\Cell
   */
  public function setNowrap($flag = true)
  {
    if ( $flag )
    {
      $this->attribute()->nowrap = 'nowrap';
    }
    else
    {
      $this->attribute()->delete('nowrap');
    }

    return $this;
  }

  /**
   * Set the background color of the cell
   *
   * @param string
   * @return \Metrol\HTML\Table\Cell
   */
  public function setBackgroundColor($color)
  {
    if ( strlen($color) == 0 )
    {
      $this->attribute()->deleteStyle('background-color');
    }
    else
    {
      $this->addStyle('background-color', $color);
    }

    return $this;
  }

  /**
   * Sets the text color in the cell.
   *
   * @param string
   * @return \Metrol\HTML\Table\Cell
   */
  public function setTextColor($color)
  {
    if ( strlen($color) == 0 )
    {
      $this->attribute()->deleteStyle('color');
    }
    else
    {
      $this->addStyle('color', $color);
    }

    return $this;
  }

  /**
   * Sets all the text in the cell to bold
   *
   * @param boolean
   * @return \Metrol\HTML\Table\Cell
   */
  public function setBold($flag = true)
  {
    if ( $flag )
    {
      $this->addStyle('font-weight', 'bold');
    }
    else
    {
      $this->attribute()->deleteStyle('font-weight');
    }

    return $this;
  }
}
