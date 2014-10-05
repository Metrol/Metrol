<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module\Boot;

/**
 * Modules that will be utilizing HTML templates should implement this interface
 * in the boot class so that the boot process knows to initiate template
 * defaults.
 *
 */
interface Template
{
  /**
   * Called to have the controller load up the routes into the Router.
   *
   */
  public function initTemplate();
}
