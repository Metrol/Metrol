<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Provides a singleton handling of a Twig template engine simplifying some
 * of the interactions
 */
class Template
{
  /**
   * The singleton instance of this class
   *
   * @var this
   */
  static protected $thisObj;

  /**
   * The Twig file loader
   *
   * @var \Twig_Loader_Filesystem
   */
  protected $loader;

  /**
   * The Twig environment object used for actual rendering
   *
   * @var \Twig_Environment
   */
  protected $twig;

  /**
   * Environment options to pass into Twig
   *
   * @var array
   */
  protected $envOptions;

  /**
   * The template to render
   *
   * @var string
   */
  protected $template;

  /**
   * List of paths to pass into the loader to tell Twig where to find the
   * templates.
   *
   * @var array
   */
  protected $templatePaths;

  /**
   * A data item filled with values to be passed into the template
   *
   * @var Metrol\Data\Item
   */
  protected $templateData;

  /**
   * Initialize the Template object
   */
  protected function __construct()
  {
    $this->envOptions    = array();
    $this->templatePaths = array();
    $this->templateData  = new \Metrol\Data\Item;
  }

  /**
   * On the assumption that all the data needing to go into Twig has been set
   * this method will now load up all the Twig objects and render an output.
   *
   * @return string The rendered output
   */
  public function render()
  {
    $this->initLoader();
    $this->initEnvironment();
    $this->initExtensions();

    return $this->twig->render($this->template,
                               $this->templateData->getValueArray());
  }

  /**
   * Get the Twig loader up and running
   */
  protected function initLoader()
  {
    if ( count($this->templatePaths) == 0 )
    {
      return '';
    }

    $paths = $this->templatePaths;

    if ( array_key_exists('__main__', $paths) )
    {
      $path = $paths['__main__'];
      unset($paths['__main__']); // Remove it from the stack
    }
    else
    {
      $msg = "Due to how Twig loads files, there must be one path provided ".
             "without a namespace specified.  Yeah, I don't like it either.";

      throw new \Metrol\Exception($msg);
    }

    $this->loader = new \Twig_Loader_Filesystem($path);

    // Twig allows for multiple paths to be assigned to a single namespace.
    // To support this need to run through the namespaces and the paths within
    // them.
    foreach ( $paths as $ns => $pathList )
    {
      foreach ( $pathList as $path )
      {
        $this->loader->addPath($path, $ns);
      }
    }
  }

  /**
   * Get the Twig environment object together
   *
   */
  protected function initEnvironment()
  {
    $this->twig = new \Twig_Environment($this->loader, $this->envOptions);
  }

  /**
   * Adds custom Metrol extensions to the Twig engine
   *
   */
  protected function initExtensions()
  {
    $this->twig->addExtension(new \Metrol\Frame\Template\Route);

    $this->twig->addFilter('dump', new \Twig_Filter_Function('var_dump'));
  }

  /**
   * Provide the object that will have it's values passed into the template to
   * be loaded.
   *
   * @return Metrol\Data\Item
   */
  public function dataObj()
  {
    return $this->templateData;
  }

  /**
   * Specifies which template will be rendered.
   *
   * @param string Template name
   * @return this
   */
  public function setTemplate($template)
  {
    $this->template = $template;

    return $this;
  }

  /**
   * Sets an environment option for Twig.  See the Twig_Environment constructor
   * comments for a proper listing of those options.
   *
   * @param string option
   * @param mixes Option value
   *
   * @return this
   */
  public function setOption($option, $value)
  {
    $this->envOptions[$option] = $value;

    return $this;
  }

  /**
   * Adds a Template path to be provided to the Twig loader
   *
   * @param string Path to the templates
   * @param string Namespace of this path
   *
   * @return this
   */
  public function setPath($path, $namespace = '__main__')
  {
    $this->templatePaths[$namespace][] = $path;

    return $this;
  }

  /**
   * Provide the one and only instance of this class
   *
   * @return \Metrol\Frame\Route\Manager
   */
  static public function getInstance()
  {
    if ( !is_object(static::$thisObj) )
    {
      $className = get_called_class();
      static::$thisObj = new $className;
    }

    return static::$thisObj;
  }
}
