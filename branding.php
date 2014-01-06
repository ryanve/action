<?php 
namespace theme;
# This gets inserted into the site header via the '@header' action. I put
# it into a separate file so that it would be easy to override just this
# component in a child theme and to facilitate sequence via '@header'
#
# Consider
# - Relevant: schema.org/Brand, schema.org/Person, schema.org/Organization
# - .site-title is common in WP but .site-name would be more systematic.
# - <hgroup> is obsolete | webmonkey.com/?p=61540 | html5doctor.com/?p=3208

call_user_func(function($hook, $tagname, $handler = 'do_action') {
  echo "<$tagname" . \rtrim(' ' . apply_filters($hook . '_atts', '')) . '>';
  $handler($hook);
  echo "</$tagname>\n\n";
}, '@branding', 'div', function() {
  echo '<h1 class="site-name site-title">';
  echo '<a itemprop="url" rel="home" href="' . home_url() . '">';
  echo '<span itemprop="name">' . get_bloginfo('name') . '</span>';
  echo '</a></h1>';
  echo apply_filters('@tagline', call_user_func(function() {
    if ( !($desc = get_bloginfo('description'))) return false;
    $type = 80 > \mb_strlen(\strip_tags($desc)) ? 'tagline subline' : 'subline';
    return "<div class='site-description $type' itemprop='description'>$desc</div>";
  }));
});

