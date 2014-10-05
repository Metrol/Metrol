<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\View;

/**
 * Base line View for all HTML views.
 */
class HTML extends \Metrol\Frame\View
{
  /**
   * @param \Metrol\Frame\Response
   */
  public function __construct(\Metrol\Frame\HTTP\Response $response)
  {
    parent::__construct($response);
  }
}
