<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module\Boot;

/**
 * Modules that will be listening to Events need to implement this so the boot
 * up knows to call initialization of listeners.
 *
 */
interface Event
{
  /**
   * Called to have the controller load up the Event Listeners to the Event
   * dispatch.
   *
   */
  public function initEventListening();
}
