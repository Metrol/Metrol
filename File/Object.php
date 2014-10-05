<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\File;

/**
 * Handles reading and writing of files
 *
 */
class Object extends \SplFileObject
{
  /**
   * Open for reading only; place the file pointer at the beginning of the file.
   *
   * @const
   */
  const READ_ONLY_EOF = 'r';

  /**
   * Open for reading and writing; place the file pointer at the beginning of
   * the file.
   *
   * @const
   */
  const READ_ONLY_BOF = 'r+';

  /**
   * Open for writing only; place the file pointer at the beginning of the file
   * and truncate the file to zero length. If the file does not exist, attempt
   * to create it.
   *
   * @const
   */
  const WRITE_ONLY = 'w';

  /**
   * Open for reading and writing; place the file pointer at the beginning of
   * the file and truncate the file to zero length. If the file does not exist,
   * attempt to create it.
   *
   * @const
   */
  const READ_WRITE = 'w+';

  /**
   * Open for writing only; place the file pointer at the end of the file.
   * If the file does not exist, attempt to create it.
   *
   * @const
   */
  const APPEND = 'a';

  /**
   * Open for reading and writing; place the file pointer at the end of the
   * file. If the file does not exist, attempt to create it.
   *
   * @const
   */
  const READ_APPEND = 'a+';

  /**
   * Initilizes the File object
   *
   * @param string
   */
  public function __construct($fileName, $mode = 'r')
  {
    parent::__construct($fileName, $mode);
  }

  /**
   * Produce the entire contents of a file as a string.
   * Essentially creating fread() for the file object.
   *
   * @return string
   */
  public function readAll()
  {
    return file_get_contents($this->getPathname());
  }

  /**
   * Provide the entire contents of the file as a base64 encoded string
   *
   * @return string
   */
  public function readAllBase64()
  {
    return base64_encode( $this->readAll() );
  }

  /**
   * Takes in a base64 encoded string, decodes it, and writes the contents out
   * to the file.
   *
   * @param string
   */
  public function writeAllBase64($encodedContent)
  {
    $this->fwrite( base64_decode($encodedContent) );
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
