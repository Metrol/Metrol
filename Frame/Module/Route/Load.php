<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module\Route;
use Metrol\Frame as mf;

/**
 * Loads the module information into the route cache
 *
 */
class Load extends mf\Route\Load
{
  /**
   * The root directory of the modules
   *
   * @param string
   */
  private $rootPath;

  /**
   * Initilizes the Load object
   *
   */
  public function __construct()
  {
    parent::__construct();

    $this->rootPath = '';
  }

  /**
   * Sets the root path where modules can be found
   *
   * @param string
   *
   * @return this
   */
  public function setRootPath($path)
  {
    $this->rootPath = $path;

    return $this;
  }

  /**
   * Works through all of the loaded Module routes and feeds enabled ones into
   * the Dispatcher for starting up.
   *
   * @return this
   */
  public function run()
  {
    if ( strlen($this->rootPath) == 0 )
    {
      print "Module Path Not Set... exiting";
      exit;
    }

    if ( !is_dir($this->rootPath) )
    {
      print "The specified module directory does not exist... exiting";
      exit;
    }

    $routes = $this->cache->getRoutes();

    foreach ( $routes as $route )
    {
      if ( !$route->isEnabled() )
      {
        continue;
      }

      $route->setRoot($this->rootPath);

      // Also need to know the source directory is there
      if ( !is_dir($route->getSource()) )
      {
        print 'The module source directory for '.$route->getName().' does '.
              'not exist: '.$route->getSource().'... exiting';

        exit;
      }

      \Metrol\Autoload::addLibrary( $route->getName(), $route->getSource() );

      $request = new mf\Module\Request($route->getName());
      $request->module = $route;

      $dispatch = new mf\Module\Dispatcher($request);
      $dispatch->run();
    }

    return $this;
  }

  /**
   * Initialize the route cache that we'll be using
   *
   */
  protected function initCache()
  {
    $this->cache = Cache::getInstance();
  }

  /**
   * Provide a new route object
   *
   * @param string Name of the route
   *
   * @return \Metrol\Frame\Route
   */
  protected function getNewRoute($routeName)
  {
    $route = new \Metrol\Frame\Module\Route($routeName);

    return $route;
  }

  /**
   * Load in the Module specific parameters that should be found in the INI file
   * for the route
   *
   * @param \Metrol\Frame\Route $route
   * @param \Metrol\Data\Item $ri Route info
   */
  protected function moreRouteInfo(\Metrol\Frame\Route $route, \Metrol\Data\Item $ri)
  {
    if ( isset($ri->description) )
    {
      $route->setDescription($ri->description);
    }

    if ( isset($ri->root) )
    {
      $route->setRoot($ri->root);
    }

    if ( isset($ri->source) )
    {
      $route->setSource($ri->source);
    }

    if ( isset($ri->config) )
    {
      $route->setConfig($ri->config);
    }

    if ( isset($ri->enabled) )
    {
      $route->setEnabled($ri->enabled);
    }
  }
}
