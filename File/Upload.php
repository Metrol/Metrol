<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\File;

/**
 * Handles uploads of files
 *
 */
class Upload
{
  /**
   * The field name in the form for the uploaded file/files
   *
   * @var string
   */
  protected $field;

  /**
   * The directory the files are stored in
   *
   * @var string
   */
  protected $repoDir;

  /**
   * The set of files to process to the repo directory
   *
   * @var array
   */
  protected $fileSet;

  /**
   * Once moved into place, this tracks the stored names of the files
   *
   * @var array
   */
  protected $repoFileSet;

  /**
   * Initilizes the File Repository object
   *
   * @param string Field name
   */
  public function __construct($fieldName)
  {
    $this->field       = $fieldName;
    $this->repoDir     = '/var/tmp/metlib';
    $this->fileSet     = array();
    $this->repoFileSet = array();
    $this->scanFlag    = false;

    $this->process();
  }

  /**
   * Moves all the files that have been uploaded to the specified repository
   * directory.
   *
   * @return this
   */
  public function moveToRepo()
  {
    if ( !is_dir($this->repoDir) )
    {
      \mkdir($this->repoDir, 0775, true);
    }

    if ( !is_dir($this->repoDir) )
    {
      print "The file repository directory could not be reached... exiting";
      exit;
    }

    foreach ( $this->fileSet as $upFile )
    {
      $fn = uniqid().'-'.$upFile->name;
      $fqn = $this->repoDir.'/'.$fn;

      // As good as uniqid is, it only takes a wee bit of time to double check
      while ( \is_file($fqn) )
      {
        $fn = uniqid().'-'.$upFile->name;
        $fqn = $this->repoDir.'/'.$fn;
      }

      $rfIdx = count($this->repoFileSet);
      $this->repoFileSet[$rfIdx] = new \stdClass;
      $this->repoFileSet[$rfIdx]->name   = $upFile->name;
      $this->repoFileSet[$rfIdx]->stored = $fn;
      $this->repoFileSet[$rfIdx]->dir    = $this->repoDir;
      $this->repoFileSet[$rfIdx]->type   = $upFile->type;
      $this->repoFileSet[$rfIdx]->size   = $upFile->size;

      move_uploaded_file($upFile->tempName, $fqn);
    }
  }

  /**
   * Provide the array of files moved to the specified repository
   *
   * @return array
   */
  public function getRepoFiles()
  {
    return $this->repoFileSet;
  }

  /**
   * Goes through every file that's been uploaded and scans them for viruses.
   * Files found to be infected will be reported back in an array.
   *
   * Errors[idx] = $obj;
   *
   * $obj->name  < infected file
   * $obj->error < What the antivirus reported
   *
   * An empty array means no viruses were found
   *
   * @return array
   */
  public function virusScan_socket()
  {
    $host   = '/var/run/clamav/clamd.ctl';
    $socket = \stream_socket_client('unix://'.$host);

    $rtn = array();

    foreach ( $this->fileSet as $upFile )
    {
      print $upFile->tempName."<br />\n";

      \fwrite($socket, 'SCAN '.$upFile->tempName);

      $response = fgets($socket);

      print $response;
    }

    return $rtn;
  }

  public function virusScan_exec()
  {
    $rtn = array();

    foreach ( $this->fileSet as $upFile )
    {
      print $upFile->tempName."<br />\n";

      $safe_path = escapeshellarg($upFile->tempName);

      $command = 'clamdscan '.$safe_path;

      $out = '';
      $int = -1;
      exec($command, $out, $int);

      var_dump($out)."<br />";
    }

    return $rtn;
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
   * Walks through all of the uploaded files from the specified field name
   * and processes them into the fileSet array as Upload\File objects
   *
   */
  private function process()
  {
    if ( !array_key_exists($this->field, $_FILES) )
    {
      print "Can't Find the Field!@!";
      exit;
    }

    $post = $_FILES[$this->field];

    if ( !array_key_exists('name', $post) )
    {
      print "Broken upload spec";
      exit;
    }

    if ( is_array($post['name']) )
    {
      $fileIdxSet = array_keys($post['name']);

      foreach ( $fileIdxSet as $fileIndex )
      {
        $upFile = new \Metrol\File\Upload\File($this->field, $fileIndex);
        $this->fileSet[] = $upFile;
      }
    }
    else
    {
      $upFile = new \Metrol\File\Upload\File($this->field);
      $this->fileSet[] = $upFile;
    }
  }
}
