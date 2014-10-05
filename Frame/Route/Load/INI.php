<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Route\Load;

/**
 * Used to create routes using an INI file
 *
 */
class INI extends \Metrol\Frame\Route\Load
{
  /**
   * The INI file to be loading from
   *
   * @var \Metrol\File\INI
   */
  protected $loadFile;

  /**
   * Initilizes the INI Load object
   *
   */
  public function __construct()
  {
    parent::__construct();

    $this->loadFile = null;
  }

  /**
   * Set the INI file to be loaded from
   *
   * @param \Metrol\File\INI
   *
   * @return this
   */
  public function setLoadFile(\Metrol\File\INI $loadFile)
  {
    $this->loadFile = $loadFile;

    return $this;
  }

  /**
   * Perform the loading into the route stack from the load file.
   *
   * @throws \Metrol\Exception when no file specified
   */
  public function load()
  {
    if ( $this->loadFile == null )
    {
      $msg = 'Load file not specified';
      $err = \Metrol\Exception::FATAL;

      throw new \Metrol\Exception($msg, $err);
    }

    $ini = $this->loadFile->parse();

    if ( isset($ini->controllerPrefix) )
    {
      $this->controllerPrefix = $ini->controllerPrefix;
      $ini->unsetField('controllerPrefix');
    }

    foreach ( $ini as $routeName => $ri )
    {
      if ( !isset($ri->controller) )
      {
        continue;
      }

      $route = $this->createRoute($routeName, $ri);

      if ( $route !== null )
      {
        $this->router->addRoute($route);
      }
    }

    return $this;
  }

  /**
   * Creates the new route and populates it from the information found in the
   * route information array.
   *
   * @param string $routeName
   * @param \Metrol\Data\Item $ri
   */
  protected function createRoute($routeName, array $ri)
  {
    $route = new \Metrol\Frame\Route($routeName);

    $this->populateActions($route, $ri);
  }
}
