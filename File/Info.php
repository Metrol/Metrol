<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\File;

/**
 * High-level object oriented interface to information for an individual file.
 */
class Info extends \SplFileInfo
{
  /**
   * Initilizes the File object
   *
   * @param string
   */
  public function __construct($fileName)
  {
    parent::__construct($fileName);

    $this->setFileClass('\Metrol\File\Object');
  }

  /**
   * Combine some common checks to make sure there's a readable file that exists
   *
   * @return boolean
   */
  public function isReadableFile()
  {
    $fn = $this->getRealPath();

    if ( $fn === false )
    {
      return false;
    }

    if ( !$this->isReadable() )
    {
      return false;
    }

    if ( !$this->isFile() )
    {
      return false;
    }

    return true;
  }
}
