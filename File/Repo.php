<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\File;

/**
 * Handles uploads and downloads of files
 *
 */
class Repo
{
  /**
   * The directory the files are stored in
   *
   * @param string
   */
  protected $repoDir;

  /**
   * Initilizes the File Repository object
   *
   */
  public function __construct()
  {
    $this->repoDir = '/var/tmp/metlib';
  }

  /**
   * Set the repository directory
   *
   * @param string
   *
   * @return this
   */
  public function setRepoDir($dir)
  {
    $this->repoDir = $dir;

    return $this;
  }

  /**
   * Gets the MIME Type of the open file
   *
   * @return string
   */
  public function getMimeType()
  {
    $fileName = $this->getPathname();

    $fileInfo = new \finfo(FILEINFO_MIME);
    $mime = $fileInfo->file($fileName);

    return $mime;
  }
}
