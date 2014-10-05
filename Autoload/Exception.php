<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Autoload;

/**
 * All data source errors should be routed through here.
 */
class Exception extends \Metrol\Exception
{
  public function __construct($errMsg)
  {
    parent::__construct($errMsg, 0);
  }

  /**
   * Adds the query in use to the message
   *
   * @param string
   * @return \Metrol\Data\Exception
   */
  public function setQuery($query)
  {
    $msg  = "\nThe following query failed\n";
    $msg .= $query;

    $this->addToMsg($msg);

    return $this;
  }
}
