<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * Handles writing out what all was sent to who through this service.
 */
class Log
{
  /**
   * The mail object to be logged
   *
   * @var \Metrol\Mail
   */
  private $mail;

  /**
   * Initilizes the Log object
   *
   * @param object
   */
  public function __construct(\Metrol\Mail $mail)
  {
    $this->mail = $mail;
  }

  /**
   * Writes out a copy of the E-Mail being sent to a plain text log file
   */
  private function logWriter()
  {
    $prefs = $GLOBALS["prefs"];
    $flag = $prefs->val("EMAIL_FLAG");

    $logFiles = array();
    $timeSent = date("Y-m-d G:i:s T");

    $logFiles[] = $prefs->val("EMAIL_LOG"); // System log file
    $logFiles[] = $this->logMailFile;       // Class setting

    foreach ($logFiles as $logFile) {
      if ( strlen($logFile) == 0 ) { continue; }
      $f = fopen($logFile, "a");

      // Check to see if any extra info should be added to the Sent: line, like
      // if the mail was actually sent and stuff like that.
      $msg = "";
      if ( !$this->sendEnabled ) {
        $msg = "[SEND DISABLED Class Flag]";
      } elseif ( !$flag ) {
        $msg = "[SEND DISABLED System Prefs]";
      } elseif ( strpos($this->redirectVal, "@") ) {
        $msg = "[MAIL REDIRECTED TO: ".$this->redirectVal."]";
      } elseif ( strpos($flag, "@") ) {
        $msg = "[MAIL REDIRECTED TO: $flag]";
      }

      $sep = str_repeat("-=", 38)."\n\n";

      fwrite($f, $sep);
      fwrite($f, "Sent: $timeSent $msg\n");
      fwrite($f, $this->display()."\n\n");
      fclose($f);
      //chmod($logFile, 0666); erroring out when sending emails via cron RJW
    }
  }
}
