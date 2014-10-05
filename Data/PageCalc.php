<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data;

/**
 * A quick utility class to perform page calculations.
 *
 */
class PageCalc
{
  /**
   * Maximum results per page
   *
   * @var integer
   */
  public $max;

  /**
   * How many items came back
   *
   * @var integer
   */
  public $res;

  /**
   * Instantiate the object
   *
   * @param integer Maximum items per page
   * @param integer Number of items in the set
   */
  public function __construct($max = 0, $res = 0)
  {
    $this->setMax($max);
    $this->setResultCount($res);
  }

  /**
   * Set the max value
   *
   * @param integer
   * @return this
   */
  public function setMax($max)
  {
    $this->max = intval($max);

    return $this;
  }

  /**
   * Set the number of items to be shown
   *
   * @param integer
   * @return this
   */
  public function setResultCount($res)
  {
    $this->res = intval($res);

    return $this;
  }

  /**
   * Get the number of pages needed to show the entire set
   *
   * @return integer
   */
  public function getPageCount()
  {
    if ( $this->res == 0 OR $this->max == 0 )
    {
      return 0;
    }

    if ( $this->res > $this->max )
    {
      $pageCount = intval(($this->res + $this->max - 1) / $this->max);
    }
    else
    {
      $pageCount = 1;
    }

    return $pageCount;
  }

  /**
   * Get the starting item number for the specified page
   *
   * @param integer The page number
   * @return integer Which item to start with for that page
   */
  public function getStartItem($pageNumber)
  {
    $pgn = intval($pageNumber);

    $pageStart = ($this->max * ($pgn - 1)) + 1;

    if ( $pageStart > $this->res )
    {
      $pageStart = $this->res;
    }

    return $pageStart;
  }

  /**
   * What to expect as the last item number for the given page
   *
   * @param integer The page number
   * @return integer Which item to end with for that page
   */
  public function getEndItem($pageNumber)
  {
    $pageStart = $this->getStartItem($pageNumber);

    $pageEnd = $pageStart + $this->max - 1;

    if ( $pageEnd > $this->res )
    {
      $pageEnd = $this->res;
    }

    return $pageEnd;
  }
}
