<?php
namespace theme;

\ob_start(function($markup) {
  # The outbut buffer usage here is experimental and powerful.
  # All content must go between ob_start and ob_get_flush.
  return apply_filters('@output', $markup);
}); 

echo "<!DOCTYPE html>\n";
# Let the entire tag be hooked so that IE conditions could be added
# See @html_atts in hooks.php for attributes
echo \ltrim(apply_filters('@html_tag', '') . "\n");

wp_head(); # Head content loads via this hook.

# Re: http://github.com/ryanve/action/commit/ee589a0bc03f5720e3e28404a6118d9934755805
echo \rtrim('<body ' . apply_filters('@body_atts', 'class="' . \implode(' ', get_body_class()) . '"')) . ">\n";

do_action('@body'); # Load all body parts via this hook.

wp_footer(); # Ensure that this is last.

\ob_get_flush();