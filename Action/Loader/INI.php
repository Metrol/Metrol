<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Action\Loader;

/**
 * Uses an INI file to load action definitions into the catalog
 *
 */
class INI extends \Metrol\Action\Loader
{
  /**
   * The INI file that will be parsed for controller::action information
   *
   * @var \Metrol\File\INI
   */
  protected $iniFile;

  /**
   * Instantiates the Load INI object
   *
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Set the INI file to be parsed
   *
   * @param \Metrol\File\INI $iniFile
   *
   * @return this
   */
  public function setINIfile(\Metrol\File\INI $iniFile)
  {
    $this->iniFile = $iniFile;

    return $this;
  }

  /**
   * Does the work of parsing the INI file and filling in the Action Catalog
   *
   * @return this
   */
  public function loadCatalog()
  {
    if ( !is_object($this->iniFile) )
    {
      return $this;  // No INI File set, get outta here quietly
    }

    // Check for a controller prefix showing up at the top of the file
    if ( isset($this->iniFile->controllerPrefix) )
    {
      $this->controllerPrefix = $this->iniFile->controllerPrefix;
      $this->iniFile->unsetField('controllerPrefix');
    }

    // Now walk through the rest of the action definitions
    foreach ( $this->iniFile as $actionName => $actionInfo )
    {
      if ( !isset($actionInfo->controller) )
      {
        continue;
      }

      $actionDef = $this->catalog->getNewActionDef();
      $actionDef->setName($actionName);
      $this->extractControllers($actionDef, $actionInfo);
      $this->moreActionInfo($actionDef, $actionInfo);

      $this->catalog->addActionDefinition($actionDef);
    }

    return $this;
  }

  /**
   * Handles extracting the controllers and actions from the action
   * information.
   *
   * @param \Metrol\Action\Controller
   * @param array The action information
   */
  protected function extractControllers(\Metrol\Action\Controller $actDef,
                                        array $actionInfo)
  {
    if ( is_array($actionInfo->controller) )
    {
      $this->controllerList($actDef, $actionInfo);
    }
    else
    {
      $this->controllerSingle($actDef, $actionInfo);
    }
  }

  /**
   * Deal with a single controller within an action definition
   *
   * @param \Metrol\Action\Controller
   * @param array Action information
   */
  protected function controllerSingle(\Metrol\Action\Controller $actDef,
                                        array $actionInfo)
  {
    $contClass = $actionInfo->controller;

    if ( substr($contClass, 0, 1) != '\\' )
    {
      $contClass = $this->controllerPrefix.'\\'.$contClass;
    }

    if ( is_array($actionInfo->action) )
    {
      foreach ( $actionInfo->action as $action )
      {
        $actDef->addAction($contClass, $action);
      }
    }
    else
    {
      $actDef->addAction($contClass, $actionInfo->action);
    }
  }

  /**
   * Deal with a list of controllers within a single action definition
   *
   * @param \Metrol\Action\Controller
   * @param array Action information
   */
  protected function controllerList(\Metrol\Action\Controller $actDef,
                                    array $actionInfo)
  {
    foreach ( $actionInfo->controller as $cIdx => $controllerClass )
    {
      if ( substr($controllerClass, 0, 1) != '\\' )
      {
        $controllerClass = $this->controllerPrefix.'\\'.$controllerClass;
      }

      $actionKey = 'action.'.$cIdx;

      if ( !array_key_exists($actionKey, $actionInfo) )
      {
        continue; // No action defined
      }

      if ( is_array($actionInfo[$actionKey]) )
      {
        foreach ( $actionInfo[$actionKey] as $action )
        {
          $actDef->addAction($controllerClass, $action);
        }
      }
      else
      {
        $actDef->addAction($controllerClass, $actionInfo[$actionKey]);
      }
    }
  }

  /**
   * Meant to be overridden by a child class to load in more specific action
   * information
   *
   * @param \Metrol\Action\Controller
   * @param array Action information
   */
  protected function moreActionInfo(\Metrol\Action\Controller $actDef,
                                   array $actionInfo)
  {
    return null;
  }
}
