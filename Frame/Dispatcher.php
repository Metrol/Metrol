<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Takes in requests from clients, passes those requests on to a Controller
 * based on what the Router specifies.  Can then provide a response back to
 * the client.
 */
class Dispatcher
{
  /**
   * The router that will provide which controllers and actions need to be
   * called upon based on the request object passed in here.
   *
   * @var \Metrol\Frame\Router
   */
  protected $router;

  /**
   * A Request object from a client
   *
   * @var \Metrol\Frame\Request
   */
  protected $request;

  /**
   * A Response object to provide a client
   *
   * @var \Metrol\Frame\Response
   */
  protected $response;

  /**
   * Initilizes the Dispatch object
   *
   * @param \Metrol\Frame\Request
   */
  public function __construct(Request $request)
  {
    $this->request = $request;

    $this->initResponse();
    $this->initRouter();
  }

  /**
   * Provide back the Response object that was created here, and with any luck
   * populated by a Controller.
   *
   * @return \Metrol\Frame\Response
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Based on the routes being passed back from the router, this routine will
   * call on all of the controllers and actions required for this request.
   *
   */
  public function run()
  {
    $route = $this->router->getRoute();

    if ( $route == null )
    {
      return;
    }

    $controllerClasses = $route->getControllers();

    foreach ( $controllerClasses as $cIdx => $controllerClass )
    {
      $controller = $this->initControllerObj($controllerClass);
      $actions    = $route->getActions($cIdx);
      $arguments  = $route->getArguments();

      foreach ( $actions as $action )
      {
        if ( !method_exists($controller, $action) )
        {
          print "Missing an action in the specified controller<br /><br />\n";
          print get_class($controller).'::'.$action."()<br />";
          print "Called from Route...<br />";
          print "<pre>$route</pre>\n";
          print "Exiting...";

          exit;
        }

        $controller->$action($arguments);
      }
    }
  }

  /**
   * Provides the Controller object to be called based on the class name
   * specified.
   *
   * @param string
   *
   * @return \Metrol\Frame\Controller
   */
  protected function initControllerObj($className)
  {
    if ( !class_exists($className) )
    {
      $e = new \Metrol\Exception;
      $e->setMessage("Dispatcher looking for controller: $className");

      throw $e;
    }

    $controller = new $className($this->request);
    $controller->setResponse($this->response);

    return $controller;
  }

  /**
   * Initialize the Response object
   *
   */
  protected function initResponse()
  {
    $this->response = new Response;
  }

  /**
   * Initializes the router that this object will ask how to handle requests
   *
   */
  protected function initRouter()
  {
    $this->router = new Router($this->request);
  }
}
