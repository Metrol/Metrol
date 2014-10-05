<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table\Effects;

/**
 * Handles all the information about, and applying striping to a Table Section.
 */
class Striping
{
  /**
   * Used to specify a column or row in the settings of this class.
   *
   * @const
   */
  const COLUMN = 1;
  const ROW    = 2;

  /**
   * The Table Section to apply the striping to
   *
   * @var \Metrol\HTML\Table\Section
   */
  private $section;

  /**
   * Should the rows of the table be striped
   *
   * @var boolean
   */
  private $horizontalFlag;

  /**
   * Should the columns of the table be striped
   *
   * @var boolean
   */
  private $verticalFlag;

  /**
   * List of the colors that should by cycled through when alternating rows
   *
   * @var array
   */
  private $horizontalColorList;

  /**
   * List of the colors that should by cycled through when alternating columns
   *
   * @var array
   */
  private $verticalColorList;

  /**
   * List of CSS Classes that will alternatively be applied to cells in a row.
   *
   * @var array
   */
  private $horizontalClassList;

  /**
   * List of CSS Classes that will alternatively be applied to cells in a
   * column.
   *
   * @var array
   */
  private $verticalClassList;

  /**
   * When there is a conflict between which color to highlight, the row or
   * column, which one should win.
   *
   * @var integer
   */
  private $resolveConflictFlag;


  /**
   * @param \Metrol\HTML\Table\Section
   */
  public function __construct(\Metrol\HTML\Table\Section $section)
  {
    $this->section              = $section;
    $this->horizontalFlag       = false;
    $this->verticalFlag         = false;
    $this->horizontalColorList  = array();
    $this->verticalColorList    = array();
    $this->horizontalClassList  = array();
    $this->verticalColorList    = array();
    $this->resolveConflictFlag = self::ROW;
  }

  /**
   * When rows and columns are enabled there will be areas that must be one or
   * the other.  This flag will determine which one wins.
   *
   * @param integer
   * @return \Metrol\HTML\Table\Striping
   */
  public function setConflictWinner($flag)
  {
    switch ($flag)
    {
      case self::ROW:
        $this->resolveConflictFlag = self::ROW;
        break;

      case self::COLUMN:
        $this->resolveConflictFlag = self::COLUMN;
        break;
    }

    return $this;
  }

  /**
   * Enables/Disables the horizontal striping
   *
   * @param boolean
   * @return \Metrol\HTML\Table\Striping
   */
  public function enableRows($flag = true)
  {
    if ( $flag )
    {
      $this->horizontalFlag = true;
    }
    else
    {
      $this->horizontalFlag = false;
    }

    return $this;
  }

  /**
   * Enables/Disables the vertical striping
   *
   * @param boolean
   * @return \Metrol\HTML\Table\Striping
   */
  public function enableColumns($flag = true)
  {
    if ( $flag )
    {
      $this->verticalFlag = true;
    }
    else
    {
      $this->verticalFlag = false;
    }

    return $this;
  }

  /**
   * Adds a color to the list of horizontal striping.
   * If only 1 color is specified, the rows will alternate between that color
   * and nothing specified.
   * Any more than 1 color, and every row's cells will rotate through the
   * specified colors.
   *
   * @param string
   * @return \Metrol\HTML\Table\Striping
   */
  public function setRowColor($color)
  {
    $this->horizontalColorList[] = $color;
    $this->enableRows(true);

    return $this;
  }

  /**
   * Adds a color to the list of vertical striping.
   * If only 1 color is specified, the columns will alternate between that color
   * and nothing specified.
   * Any more than 1 color, and every columns's cells will rotate through the
   * specified colors.
   *
   * @param string
   * @return \Metrol\HTML\Table\Striping
   */
  public function setColumnColor($color)
  {
    $this->verticalColorList[] = $color;
    $this->enableColumns(true);

    return $this;
  }

  /**
   * Removes all the colors from both the rows and columns
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function resetAllColors()
  {
    $this->resetRowColors();
    $this->resetColumnColors();

    return $this;
  }

  /**
   * Removes all the colors that have been assigned to the rows
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function resetRowColors()
  {
    $this->horizontalColorList = array();

    return $this;
  }

  /**
   * Removes all the colors that have been assigned to the rows
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function resetColumnColors()
  {
    $this->verticalColorList = array();

    return $this;
  }

  /**
   * Adds a CSS Class to the rows
   *
   * @param string
   * @return \Metrol\HTML\Table\Striping
   */
  public function setRowClass($className)
  {
    $this->horizontalClassList[] = $className;
    $this->enableRows(true);

    return $this;
  }

  /**
   * Adds a CSS Class to the columns
   *
   * @param string
   * @return \Metrol\HTML\Table\Striping
   */
  public function setColumnClass($className)
  {
    $this->verticalClassList[] = $className;
    $this->enableColumns(true);

    return $this;
  }

  /**
   * Removes all the class assignments from both the rows and columns
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function resetAllClasses()
  {
    $this->resetRowClasses();
    $this->resetColumnClasses();

    return $this;
  }

  /**
   * Removes all the classes that have been assigned to the rows
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function resetRowClasses()
  {
    $this->horizontalClassList = array();

    return $this;
  }

  /**
   * Removes all the colors that have been assigned to the rows
   *
   * @return \Metrol\HTML\Table\Striping
   */
  public function resetColumnClasses()
  {
    $this->verticalClassList = array();

    return $this;
  }

  /**
   * Takes all the settings that have come in here and applies them to the
   * Table Section.
   */
  public function apply()
  {
    switch ( $this->resolveConflictFlag )
    {
      case self::COLUMN:
        $this->applyHorizontal();
        $this->applyVertical();
        break;

      case self::ROW:
        $this->applyVertical();
        $this->applyHorizontal();
        break;
    }
  }

  /**
   * Apply the striping to the columns
   */
  private function applyVertical()
  {
    if ( !$this->verticalFlag )
    {
      return; // Vertical striping not enabled.
    }

    $colorCount = count($this->verticalColorList);
    $classCount = count($this->verticalClassList);

    if ( $colorCount == 0 and $classCount == 0 )
    {
      return;
    }

    $classes    = array();
    $colors     = array();

    if ( $colorCount > 0 )
    {
      // For only one color specified, toggle between on and off
      if ( $colorCount == 1 )
      {
        $colors[] = '';
        $colors[] = reset($this->verticalColorList);
      }
      // Otherwise, just use the color list.
      else
      {
        $colors = $this->verticalColorList;
      }
    }

    if ( $classCount > 0 )
    {
      // For only one color specified, toggle between on and off
      if ( $classCount == 1 )
      {
        $classes[] = '';
        $classes[] = reset($this->verticalClassList);
      }
      // Otherwise, just use the color list.
      else
      {
        $classes = $this->verticalClassList;
      }
    }


    foreach ( $this->section as $row )
    {
    reset($colors); // Make sure we're at the top of the stack
    reset($classes);

      foreach ( $row as $cell )
      {
        $color = current($colors); // Where the stack is now
        $class = current($classes);

        if ( strlen($color) > 0 )
        {
          $cell->setBackgroundColor($color);
        }

        if ( strlen($class) > 0 )
        {
          $cell->setClass($class);
        }

        // Increment and reset as needed to the list of colors and classes
        if ( next($colors) === false )
        {
          reset($colors);
        }

        if ( next($classes) === false )
        {
          reset($classes);
        }
      }
    }
  }

  /**
   * Apply the striping to the rows
   */
  private function applyHorizontal()
  {
    if ( !$this->horizontalFlag )
    {
      return; // Horizontal striping not enabled.
    }

    $colorCount = count($this->horizontalColorList);
    $classCount = count($this->horizontalClassList);

    if ( $colorCount == 0 and $classCount == 0 )
    {
      return;
    }

    $classes    = array();
    $colors     = array();

    if ( $colorCount > 0 )
    {
      // For only one color specified, toggle between on and off
      if ( $colorCount == 1 )
      {
        $colors[] = '';
        $colors[] = reset($this->horizontalColorList);
      }
      // Otherwise, just use the color list.
      else
      {
        $colors = $this->horizontalColorList;
      }
    }

    if ( $classCount > 0 )
    {
      // For only one color specified, toggle between on and off
      if ( $classCount == 1 )
      {
        $classes[] = '';
        $classes[] = reset($this->horizontalClassList);
      }
      // Otherwise, just use the color list.
      else
      {
        $classes = $this->horizontalClassList;
      }
    }

    reset($colors); // Make sure we're at the top of the stack
    reset($classes);

    // Walk each row
    foreach ( $this->section as $row )
    {
      $color = current($colors); // Where the stack is now
      $class = current($classes);

      if ( strlen($color) > 0 )
      {
        $row->styleCells('background-color', $color);
      }

      if ( strlen($class) > 0 )
      {
        foreach ( $row as $cell )
        {
          $cell->setClass($class);
        }
      }

      // Increment and reset as needed to the list of colors and classes
      if ( next($colors) === false )
      {
        reset($colors);
      }

      if ( next($classes) === false )
      {
        reset($classes);
      }
    }
  }
}
