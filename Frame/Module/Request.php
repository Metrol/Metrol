<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module;

/**
 * The object will be passed to a Module controller with basic information it
 * will need to get things rolling.
 */
class Request extends \Metrol\Frame\Request
{
  /**
   * Name of the Module
   *
   * @var string
   */
  protected $moduleName;

  /**
   * Initilizes the Moudle Request object
   *
   * @param string Name of the Module
   */
  public function __construct($moduleName)
  {
    parent::__construct();

    $this->moduleName = $moduleName;
  }

  /**
   * Provide the name of the Module this request is for
   *
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }
}
