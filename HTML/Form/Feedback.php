<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * Used by form processing script to store the errors and information to be
 * passed back to objects needing to provide feedback or take follow up actions
 * to a submit.
 */
class Feedback
{
  /**
   * List of errors indexed by field
   *
   * @var array
   */
  protected $errors;

  /**
   * List of information values to be passed back from the form processing
   *
   * @var array
   */
  protected $info;

  /**
   * Initialize the member variables
   */
  public function __construct()
  {
    $this->errors = array();
    $this->info   = array();
  }

  /**
   * Adds an error message to the stack
   *
   * @param string Name of the field to associate the error with
   * @param string The message for the error
   * @return this
   */
  public function addError($fieldName, $errorMessage)
  {
    $this->errors[$fieldName] = htmlentities($errorMessage);

    return $this;
  }

  /**
   * Add some type of information to be reported back to the caller of the
   * form processing.
   *
   * @param string Key for the information
   * @param string Some kind of information to be passed along
   * @return this
   */
  public function addInfo($key, $info)
  {
    $this->info[$key] = $info;

    return $this;
  }

  /**
   * Checks to see if any errors have been registred here
   *
   * @return boolean
   */
  public function anyErrors()
  {
    $rtn = false;

    if ( count($this->errors) > 0 )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Produce a JSON (Javascript Object Notation) string representation of the
   * errors and information stored here.
   */
  public function getJSON()
  {
    $result = array('error', 'info');

    $result['error'] = $this->errors;
    $result['info']  = $this->info;

    $resultJSON = \json_encode($result);

    return $resultJSON;
  }
}
