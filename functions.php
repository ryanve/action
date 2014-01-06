<?php
/**
 * @link http://actiontheme.com
 * @link http://github.com/ryanve/action
 */
 
if (0 < version_compare('5.3.0', phpversion())) {
  # Fail gracefully < 5.3. Tested in 5.2. Users return to the "Themes" page.
  echo '<strong role="alert">';
  trigger_error(implode("<br>\n", array(
      __('PHP 5.3+ required.', 'theme')
    , __('Please upgrade PHP via your web host.', 'theme')
    , '<a href="' . admin_url('themes.php') . '">' . __('Or, switch themes.', 'theme') . "</a>"
  )) . '<hr>', E_USER_WARNING);
  echo '</strong>';
} else require 'hooks.php';