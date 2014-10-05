<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\DSN;

/**
 * Used to populate the DSN Manager with routes from various sources
 *
 */
class Loader
{
  /**
   * The DSN Manager to be worked with here
   *
   * @var \Metrol\Db\DSN\Manager
   */
  protected $dsnMgr;

  /**
   * Instantiate the Loader object
   *
   */
  public function __construct()
  {
  	$this->initManager();
  }

  /**
   * Instantiate the DSN Manager to load DSNs into
   *
   */
  protected function initManager()
  {
  	$this->dsnMgr = \Metrol\Db\DSN\Manager::getInstance();
  }
}
