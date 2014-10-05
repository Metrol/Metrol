<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Used to specify which View is needed for the application.
 */
class Controller extends \Metrol\Frame\Controller
{
  /**
   * @param \Metrol\Frame\HTTP\Request
   */
  public function __construct(Request $request, Response $response)
  {
    parent::__construct($request);

    $this->setResponse($response);
  }

  /**
   * Forces an HTTP redirect to the specified route with optional arguments
   *
   * @param string Route name
   * @param integer HTTP status
   * @param array List of argument to apply to the route
   */
  public function redirectToRoute($routeName, $status = 200, array &$args = null)
  {
    $route = \Metrol\Frame\HTTP\Route\Cache::getInstance()
            ->getRoute($routeName);

    if ( is_array($args) )
    {
      $route->clearArguments();

      foreach ( $args as $arg )
      {
        $route->addArguments($arg);
      }
    }

    \header('Location: '.$route->getURL(), intval($status));
    exit;
  }
}
