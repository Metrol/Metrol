<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module;

/**
 * When a Module has been enabled, a child of this class will be called to boot
 * that module up.
 *
 */
class Boot extends \Metrol\Frame\Controller
{
  /**
   * Initilizes the Controller Boot object
   *
   * @param \Metrol\Frame\Module\Request
   */
  public function __construct(Request $request)
  {
    parent::__construct($request);
  }

  /**
   * A default boot up script for this Module that attempts to load common
   * configuration files.
   *
   */
  public function load()
  {
    $this->loadEventListeners();
    $this->loadHTTPRoutes();

    // Kick off an Event to announce the module being loaded
    $event = new \Metrol\Frame\Event('Module Loaded');
    $event->moduleName = $this->request->module->getName();
    $event->setMessage('Started up a module');
    $event->run();
  }

  /**
   * Pretty good default to loading up HTTP routes
   *
   */
  protected function loadHTTPRoutes()
  {
    $conf = $this->request->module->getConfig();
    $file = $conf.'/http_routes.ini';

    $routeINI = new \Metrol\File\INI($file);

    if ( $routeINI->isReadableFile() )
    {
      $routeINI->parse();
      $loader = new \Metrol\Frame\HTTP\Route\Loader;
      $loader->loadFromINI($routeINI);
    }
  }

  /**
   * Default process for loading up Event listeners
   *
   */
  protected function loadEventListeners()
  {
    $conf = $this->request->module->getConfig();

    $file = $conf.'/events.ini';

    if ( is_file($file) and is_readable($file) )
    {
      new \Metrol\Frame\Event\Route\Load($conf.'/events.ini');
    }
  }
}
