<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Description of DateTime
 */
class DateTime extends \Metrol\Data\Type
{
  /**
   * Minimum date allowed
   *
   * @var \Metrol\Date
   */
  private $minDate;

  /**
   * Initialize the DateTime object
   *
   * @param object
   */
  public function __construct()
  {
    parent::__construct();

    $this->minDate = null; // Sets it up for no minimum
  }

  /**
   *
   */
}
