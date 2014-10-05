<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\File\Upload;

/**
 * Describes an uploaded file
 *
 */
class File
{
  /**
   * The field name used by the HTML form to upload the file
   *
   * @var string
   */
  private $field;

  /**
   * Which array index in the $_FILES global has the file we're interested in
   *
   * @var integer
   */
  private $index;

  /**
   * The name of the uploaded file
   *
   * @var string
   */
  public $name;

  /**
   * The temporary name of the uploaded file
   *
   * @var string
   */
  public $tempName;

  /**
   * The error code that came back from the upload attempt
   *
   * @var integer
   */
  public $error;

  /**
   * Size of the file coming on up
   *
   * @var integer
   */
  public $size;

  /**
   * The Mime type of the file
   *
   * @var string
   */
  public $type;

  /**
   * Initilizes the File Upload Descriptor object
   *
   */
  public function __construct($fieldName, $index = null)
  {
    $this->field = $fieldName;

    if ( $index !== null )
    {
      $this->index = intval($index);
    }
    else
    {
      $this->index = null;
    }

    $this->initDescription();
  }

  /**
   * Populate the member variables of this object from the global FILES var
   *
   */
  private function initDescription()
  {
    if ( !array_key_exists($this->field, $_FILES) )
    {
      throw new \Metrol\Exception('Upload form field does not exist');
    }

    $post = $_FILES[$this->field];

    if ( !array_key_exists('name', $post) )
    {
      throw new \Metrol\Exception('Problems with the $_FILE global');
    }

    if ( $this->index !== null )
    {
      if ( !is_array($post['name']) )
      {
        throw new \Metrol\Exception('Index specified, only one file uploaded');
      }

      if ( !array_key_exists($this->index, $post['name']) )
      {
        $msg = 'File index: '.$this->index.' not found in $_FILE global';
        throw new \Metrol\Exception($msg);
      }

      $this->name     = $post['name'][$this->index];
      $this->tempName = $post['tmp_name'][$this->index];
      $this->error    = $post['error'][$this->index];
      $this->size     = \filesize($this->tempName);
      $this->type     = $this->getMimeType();
    }

    if ( $this->index === null )
    {
      if ( is_array($post['name']) )
      {
        throw new \Metrol\Exception('No index specified, many files uploaded');
      }

      $this->name     = $post['name'];
      $this->tempName = $post['tmp_name'];
      $this->error    = $post['error'];
      $this->size     = \filesize($this->tempName);
      $this->type     = $this->getMimeType();
    }
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
   * Gets the MIME Type of the temporary file
   *
   * @return string
   */
  private function getMimeType()
  {
    $fileInfo = new \finfo(FILEINFO_MIME);
    $mime = $fileInfo->file($this->tempName);

    return $mime;
  }
}
