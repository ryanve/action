<?php
namespace theme;

\ob_start(function($markup) {
  return apply_filters('@output', $markup);
}); 

echo "<!DOCTYPE html>\n";

echo apply_filters('@html_tag', '');

wp_head(); # Head parts loads via this hook.

echo apply_filters('@body_tag', '');

do_action('@body'); # Body parts load via this hook.

wp_footer(); # Ensure that this is last.

\ob_get_flush();