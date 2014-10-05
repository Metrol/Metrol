<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * This is an RFC-3696 compliant EMail Address validator.
 *
 * Original source code came from: http://code.iamcal.com/php/rfc822/
 * As I got this code it didn't work.  Several fixes were required with the
 * array handling.
 */
class Validate
{
  /**
   * The address being tested
   *
   * @var string
   */
  private $email;

  /**
   * Stores the supplied Email address for later processing.
   *
   * @param string
   */
  public function __construct($emailAddress = '')
  {
    $this->email = $emailAddress;
  }

  /**
   * Used to set the Email address to a new value
   *
   * @param string
   * @return this
   */
  public function setEmail($emailAddress)
  {
    $this->email = substr($emailAddress, 0, 255);

    return $this;
  }

  /**
   * If the address passed in is good, a boolean TRUE is returned.
   *
   * @return boolean
   */
  public function validate()
  {
    $email = $this->email;

    $no_ws_ctl = "[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]";
    $alpha     = "[\\x41-\\x5a\\x61-\\x7a]";
    $digit     = "[\\x30-\\x39]";
    $cr        = "\\x0d";
    $lf        = "\\x0a";
    $crlf      = "(?:$cr$lf)";

    $obs_char  = "[\\x00-\\x09\\x0b\\x0c\\x0e-\\x7f]";
    $obs_text  = "(?:$lf*$cr*(?:$obs_char$lf*$cr*)*)";
    $text      = "(?:[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f]|$obs_text)";

    $text        = "(?:$lf*$cr*$obs_char$lf*$cr*)";
    $obs_qp      = "(?:\\x5c[\\x00-\\x7f])";
    $quoted_pair = "(?:\\x5c$text|$obs_qp)";
    $wsp         = "[\\x20\\x09]";
    $obs_fws     = "(?:$wsp+(?:$crlf$wsp+)*)";
    $fws         = "(?:(?:(?:$wsp*$crlf)?$wsp+)|$obs_fws)";
    $ctext       = "(?:$no_ws_ctl|[\\x21-\\x27\\x2A-\\x5b\\x5d-\\x7e])";
    $ccontent    = "(?:$ctext|$quoted_pair)";
    $comment     = "(?:\\x28(?:$fws?$ccontent)*$fws?\\x29)";
    $cfws        = "(?:(?:$fws?$comment)*(?:$fws?$comment|$fws))";

    $outer_ccontent_dull = "(?:$fws?$ctext|$quoted_pair)";
    $outer_ccontent_nest = "(?:$fws?$comment)";

    $outer_comment  = "(?:\\x28$outer_ccontent_dull*";
    $outer_comment .= "(?:$outer_ccontent_nest$outer_ccontent_dull*)+$fws?\\x29)";

    $atext  = "(?:$alpha|$digit|[\\x21\\x23-\\x27\\x2a\\x2b\\x2d\\x2f\\x3d";
    $atext .= "\\x3f\\x5e\\x5f\\x60\\x7b-\\x7e])";

    $atom   = "(?:$cfws?(?:$atext)+$cfws?)";
    $qtext  = "(?:$no_ws_ctl|[\\x21\\x23-\\x5b\\x5d-\\x7e])";

    $qcontent       = "(?:$qtext|$quoted_pair)";
    $quoted_string  = "(?:$cfws?\\x22(?:$fws?$qcontent)*$fws?\\x22$cfws?)";
    $quoted_string  = "(?:$cfws?\\x22(?:$fws?$qcontent)+$fws?\\x22$cfws?)";
    $word           = "(?:$atom|$quoted_string)";
    $obs_local_part = "(?:$word(?:\\x2e$word)*)";
    $obs_domain     = "(?:$atom(?:\\x2e$atom)*)";
    $dot_atom_text  = "(?:$atext+(?:\\x2e$atext+)*)";
    $dot_atom       = "(?:$cfws?$dot_atom_text$cfws?)";
    $dtext          = "(?:$no_ws_ctl|[\\x21-\\x5a\\x5e-\\x7e])";
    $dcontent       = "(?:$dtext|$quoted_pair)";
    $domain_literal = "(?:$cfws?\\x5b(?:$fws?$dcontent)*$fws?\\x5d$cfws?)";

    $local_part     = "(($dot_atom)|($quoted_string)|($obs_local_part))";
    $domain         = "(($dot_atom)|($domain_literal)|($obs_domain))";
    $addr_spec      = "$local_part\\x40$domain";

    // The over all length of the address is not to exceed 255 characters.
    if ( strlen($email) > 255 )
    {
      return FALSE;
    }

    $email = $this->stripComments($outer_comment, $email, "(x)");

    // Now match what's left
    if ( !preg_match("!^$addr_spec$!", $email, $m) )
    {
      return FALSE;
    }

    $bits = array(
      'local'          => $m[1],
      'local-atom'     => $m[2],
      'local-quoted'   => $m[3],
      'local-obs'      => $m[4],
      'domain'         => $m[5],
      'domain-atom'    => $m[6]
    );

    // Added these checks for the literal and obs since we can't be sure they
    // will come through or not.
    if ( array_key_exists(7, $m) )
    {
      $bits['domain-literal'] = $m[7];
    }
    else
    {
      $bits['domain-literal'] = "";
    }

    if ( array_key_exists(8, $m) )
    {
      $bits['domain-obs'] = $m[8];
    }
    else
    {
      $bits['domain-obs'] = "";
    }

    // We need to now strip comments from $bits[local] and $bits['domain'],
    // since we know they're i the right place and we want them out of the
    // way for checking IPs, label sizes, etc
    //
    $bits['local']  = $this->stripComments($comment, $bits['local']);
    $bits['domain'] = $this->stripComments($comment, $bits['domain']);


    // Length limits on segments
    if (strlen($bits['local']) > 64)
    {
      return FALSE;
    }

    if (strlen($bits['domain']) > 255)
    {
      return FALSE;
    }

    // Restrictions on domain-literals from RFC2821 section 4.1.3
    //
    if ( strlen($bits['domain-literal']) )
    {
      $Snum                  = "(\d{1,3})";
      $IPv4_address_literal  = "$Snum\.$Snum\.$Snum\.$Snum";

      $IPv6_hex       = "(?:[0-9a-fA-F]{1,4})";
      $IPv6_full      = "IPv6\:$IPv6_hex(:?\:$IPv6_hex){7}";
      $IPv6_comp_part = "(?:$IPv6_hex(?:\:$IPv6_hex){0,5})?";
      $IPv6_comp      = "IPv6\:($IPv6_comp_part\:\:$IPv6_comp_part)";
      $IPv6v4_full    = "IPv6\:$IPv6_hex(?:\:$IPv6_hex){5}\:";
      $IPv6v4_full   .= $IPv4_address_literal;

      $IPv6v4_comp_part = "$IPv6_hex(?:\:$IPv6_hex){0,3}";
      $IPv6v4_comp      = "IPv6\:((?:$IPv6v4_comp_part)?\:\:(?:";
      $IPv6v4_comp     .= $IPv6v4_comp_part."\:)?)$IPv4_address_literal";

      // IPv4 is simple
      if ( preg_match('!^\['.$IPv4_address_literal.'\]$!', $bits['domain'], $m))
      {
        if (intval($m[1]) > 255)
        {
          return FALSE;
        }

        if (intval($m[2]) > 255)
        {
          return FALSE;
        }

        if (intval($m[3]) > 255)
        {
          return FALSE;
        }

        if (intval($m[4]) > 255)
        {
          return FALSE;
        }
      }
      else
      {
        // This should be IPv6 - a bunch of tests are needed here :)

        while (1)
        {
          if ( preg_match('!^\['.$IPv6_full.'\]$!', $bits['domain']) )
          {
            break;
          }

          if ( preg_match('!^\['.$IPv6_comp.'\]$!', $bits['domain'], $m) )
          {
            list($a, $b) = explode('::', $m[1]);

            $folded = (strlen($a) && strlen($b)) ? "$a:$b" : "$a$b";
            $groups = explode(':', $folded);

            if ( count($groups) > 6 )
            {
              return FALSE;
            }

            break;
          }

          if ( preg_match('!^\['.$IPv6v4_full.'\]$!', $bits['domain'], $m) )
          {
            if (intval($m[1]) > 255)
            {
              return FALSE;
            }

            if (intval($m[2]) > 255)
            {
              return FALSE;
            }

            if (intval($m[3]) > 255)
            {
              return FALSE;
            }

            if (intval($m[4]) > 255)
            {
              return FALSE;
            }

            break;
          }

            if ( preg_match('!^\['.$IPv6v4_comp.'\]$!', $bits['domain'], $m) )
            {
              list($a, $b) = explode('::', $m[1]);
              $b = substr($b, 0, -1); // Remove the trailing colon before
                                      // the IPv4 address
              $folded = (strlen($a) && strlen($b)) ? "$a:$b" : "$a$b";

              $groups = explode(':', $folded);

              if (count($groups) > 4)
              {
                return FALSE;
              }

              break;
            }

          return FALSE;
        }
      }

    } else {

      // The domain is either dot-atom or obs-domain - either way, it's
      // made up of simple labels and we split on dots
      //
      $labels = explode('.', $bits['domain']);

      // This is allowed by both dot-atom and obs-domain, but is un-routeable on the
      // public internet, so we'll fail it (e.g. user@localhost)
      //
      if ( count($labels) == 1 )
      {
        return FALSE;
      }

      // Checks on each label
      foreach ( $labels as $label )
      {
        if ( strlen($label) > 63 )
        {
          return FALSE;
        }

        if ( substr($label, 0, 1) == '-' )
        {
          return FALSE;
        }

        if ( substr($label, -1) == '-' )
        {
          return FALSE;
        }
      }

      // Last label can't be all numeric
      if ( preg_match('!^[0-9]+$!', array_pop($labels)) )
      {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Used to strip comments from an Email address
   *
   * @param string
   * @param string
   * @param string
   */
  private function stripComments($comment, $email, $replace='')
  {
    while (true)
    {
      $new = preg_replace('!'.$comment.'!', $replace, $email);

      if ( strlen($new) == strlen($email) )
      {
        return $email;
      }

      $email = $new;
    }
  }
}
