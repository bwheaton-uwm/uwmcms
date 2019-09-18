<?php

/**
 * Confirms pre-production users have a custom auth cookie.
 *
 * This script checks for an authorization cookie if the script is
 * included on our site code. Currently, it's included in settings.php,
 * if the current environment (host) is Acquia dev, stage or ra.
 *
 * The script checks for the cookie and if missing, presents the user with a
 * form to set the cookie. It can be disabled (without needing a release to servers)
 * by setting a disable check flag in one of the configuration files that does not
 * require deployments.
 *
 * To disabable the check, remove `include 'uwm-require-auth-cookie-page-blocker.php'`
 * from our settings file or add `$_ENV['skipuwm-377211022-pre-production'] = 1;`
 * to a settings file.
 *
 * The following settings files do not require build releases:
 *
 * @see /var/www/site-php/uwmed/stevie-settings.inc
 * @see /mnt/gfs/%s.%s/nobackup/stevie.secrets.settings.php
 */

if (!class_exists('UwmAuthCookieFormBlocker')) {

  /**
   * Controller to check an auth cookie, or die with an auth form.
   */
  class UwmAuthCookieFormBlocker
  {

    CONST BLOCKED_ENVIRONMENTS = ['dev', 'stage', 'ra'];

    CONST UNIVERSITY_RANGE_IPS = [
      '128.208.0.0/16',
      '128.95.0.0/16',
      '140.142.0.0/16',
      '198.48.64.0/19',
      '205.175.96.0/19',
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

      $cookieKeyName = self::getCookieKeyName();


      if (!$isAllowed) {

        // Set the auth cookie, if posted:
        if (isset($_POST[$cookieKeyName]) && ($_POST[$cookieKeyName] === $cookieKeyName)) {

          setcookie($cookieKeyName, $cookieKeyName, strtotime("+1 month"), '/');
          header("Location: " . $_POST['destination'] ?? '/');

        } else if ($_COOKIE[$cookieKeyName] === $cookieKeyName) {

          // Do nothing.

        } else {

          // Bypass Varnish caching:
          // $conf['cache'] = '0';
          header('Cache-Control: max-age=-1');


          // Die with cookie form:
          self::getAuthForm();
          exit;

        }

      }

    }

    /**
     * Returns the name of the cookie to set.
     *
     * @return string
     *   String cookie name for headers.
     */
    private static function getCookieKeyName() {
      return str_replace('.', '_', 'uwm.66782344.' . $_SERVER['SERVER_NAME']);
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
      if (!in_array($_ENV['AH_SITE_ENVIRONMENT'], self::BLOCKED_ENVIRONMENTS)) {
        return TRUE;
      }

      // Do not test drush or Travis.
      if (!empty(PHP_SAPI) && PHP_SAPI === 'cli') {
        return TRUE;
      }

      // Do not test if some override.
      if (!empty($_ENV['skip' . self::getCookieKeyName()])) {
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

      $destination = $_SERVER['REQUEST_URI'];
      $cookie_name = self::getCookieKeyName();
      $footer = $_SERVER['SERVER_SOFTWARE'] . ' ' . $_SERVER['SCRIPT_FILENAME'] . ' ' . $cookie_name;

      print <<< AUTHFORM

<html>
<head>
<title>Verify Authentication</title>
</head>
<body>
<pre>

Please provide a code for $cookie_name

<form method="post">

    Pass code:   <input type="text" name="$cookie_name">
                 <input type="hidden" name="destination" value="$destination">

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

}


$shieldSite = new UwmAuthCookieFormBlocker();
