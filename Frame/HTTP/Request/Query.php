<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Parent class for handling HTTP Query arrays such as GET, POST, COOKIE, etc
 *
 */
class Query extends \Metrol\Data\Item
{
  /**
   * The original array that was passed in here.
   *
   * @var array
   */
  protected $origValues;

  /**
   * Initiates the Query object
   */
  public function __construct(array &$query)
  {
    parent::__construct();

    $this->origValues &= $query;

    foreach ( $query as $key => $val )
    {
      $this->setValue($key, $val);
    }
  }

  /**
   * Diagnostic output showing the contents of this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = 'Contents of '.get_class($this)."\n";
    $rtn .= "  ----------------------\n";

    if ( count($this->dataItem) == 0 )
    {
      $rtn .= '  |     [Empty]'."\n";
    }

    foreach ( $this as $key => $val )
    {
      $rtn .= "  | $key = ";

      if ( is_array($val) )
      {
        $rtn .= print_r($val, true);
      }
      else
      {
        $rtn .= $val."\n";
      }
    }

    $rtn .= "  ----------------------\n";

    return $rtn;
  }

  /**
   * This pushes all the values that have been stored here to the actual
   * super global.  Meant for Sessions and Cookies mostly.
   *
   */
  public function save()
  {
    foreach ( $this as $key => $val )
    {
      $this->origValues[$key] = $val;
    }
  }

  /**
   * Provide a URL ready GET string based on the data stored here
   *
   * @return string
   */
  public function getUrlQuery()
  {
    if ( count($this->dataItem) == 0 )
    {
      return '';
    }

    $qry = '?';

    foreach ( $this as $key => $val )
    {
      $enKey = rawurlencode($key);
      $enVal = rawurlencode($val);

      $qry .= $enKey.'='.$enVal.'&';
    }

    $rtn = substr($qry, 0, -1);  // Strip the trailing &

    return $rtn;
  }
}
