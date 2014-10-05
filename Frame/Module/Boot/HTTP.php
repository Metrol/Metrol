<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module\Boot;

/**
 * Module boot classes that will be supporting HTTP requests will need to
 * implement this interface.  This specifies how routes are loaded.
 *
 */
interface HTTP
{
  /**
   * Called to have the controller load up the routes into the Router.
   *
   */
  public function initRoutes();
}
