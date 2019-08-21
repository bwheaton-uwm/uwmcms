<?php

$sheildSite = new UwmAuthCookieRestriction();


/**
 * Controller to check an auth cookie, or die with an auth form.
 */
class UwmAuthCookieRestriction
{

  CONST KEY_NAME = 'uwm-377211022-pre-production';

  CONST PASSWORD = 'devdev';

  CONST BLOCKED_ENVIRONMENTS = ['dev', 'stage', 'ra'];

  CONST UNIVERSITY_RANGE_IPS = [
    '128.208.0.0/16',
    '128.95.0.0/16',
    '140.142.0.0/16',
    '198.48.64.0/19',
    '69.91.128.0/17',
    '173.250.128.0/17',
    '108.179.128.0/18',
    '204.69.8.0/21',
  ];

  /**
   * UwmAuthCookieRestriction constructor.
   */
  function __construct() {


    $isAllowed = self::isClientAllowed();

    if (!$isAllowed) {

      // Set the auth cookie, if posted:
      if (isset($_POST[self::KEY_NAME]) && ($_POST[self::KEY_NAME] === self::PASSWORD)) {

        setcookie(self::KEY_NAME, $_POST[self::KEY_NAME], strtotime("+1 week"));
        header("Location: /");

      }

      // Check an auth cookie, or die with form:
      if ($_COOKIE[self::KEY_NAME] !== self::PASSWORD) {

        getAuthForm();
        exit;

      }

    }

  }


  /**
   * Check if user is on trusted network.
   *
   * @see https://itconnect.uw.edu/connect/uw-networks/network-addresses/
   * @see https://wiki.cac.washington.edu/display/UWNOC/IP+Address+Space+Usage
   *
   * @return bool
   *   If the current user is on University IP range.
   */
  public static function isClientAllowed() {

    // Do not test localhost.
    if (empty($_ENV['AH_SITE_ENVIRONMENT'])) {
      return TRUE;
    }

    // Do not check production.
    if(!in_array($_ENV['AH_SITE_ENVIRONMENT'], self::BLOCKED_ENVIRONMENTS)) {
      return TRUE;
    }

    // Do not test if some override.
    if(!empty($_ENV['skip' . self::KEY_NAME])) {
      return TRUE;
    }

    // Do not check University or internal network.
    if (function_exists('ip2long')) {

      $userIp = self::getClientIp();
      foreach (SELF::UNIVERSITY_RANGE_IPS as $uwIp) {

        list($subnet, $mask) = explode('/', $uwIp);
        $ip2 = ip2long($userIp) & ~((1 << (32 - $mask)) - 1);

        if ($ip2 == ip2long($subnet)) {
          return TRUE;
        }
      }
    }

    return FALSE;

  }


  /**
   * Returns complete HTML page and form markup.
   *
   * @param self::KEY_NAME
   *   The key for our cookie and form.
   */
  private static function getAuthForm() {

    $footer = $_SERVER['SERVER_SOFTWARE'] . ' ' . $_SERVER['SCRIPT_FILENAME'];
    $hint = self::PASSWORD;
    $auth_parameter = self::KEY_NAME;

    print <<< AUTHFORM

<html>
<head>
<title>Verify Authentication</title>
</head>
<body>
<pre>

Please authenticate. Please provide a $hint username and password.

<form method="post">

    First Name:   <input type="text" name="$auth_parameter">
    Last Name:    <input type="text" name="password">

    <button type="submit"> Submit </button>
</form>


<a href="https://www.uwmedicine.org">https://www.uwmedicine.org</a></p>
$footer


</pre>
</body>
</html>
AUTHFORM;


  }

  /**
   * Return use IP, behind load balancer.
   *
   * @return string
   *   The client IP if found, or 'UNKNOWN'.
   */
  public
  static function getClientIp() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) {
      $ipaddress = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
      $ipaddress = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')) {
      $ipaddress = getenv('HTTP_FORWARDED');
    } elseif (getenv('REMOTE_ADDR')) {
      $ipaddress = getenv('REMOTE_ADDR');
    } else {
      $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
  }

}


