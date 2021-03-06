<?php
/**
 * @link http://actiontheme.com
 * @link http://github.com/ryanve/action
 * @author Ryan Van Etten
 * @license MIT
 */

# This file loads via functions.php
# All functions in the theme are anonymous. Nothing is exposed.
# The namespace allows devs to overload in the \theme context.
namespace theme;

# Hooks created by the theme are prefixed with the '@' symbol 
# as to not conflict with hooks created by the WordPress core.
# http://codex.wordpress.org/Plugin_API/Action_Reference

# $content_width is required per http://codex.wordpress.org/Theme_Review
# http://bit.ly/content-width-zero
# Use WP -> Settings -> Media
isset($content_width) or $content_width = get_option('large_size_w');
\is_numeric($content_width) or $content_width = 1024;

# Use the generic 'theme' as the textdomain such that it is easier to 
# repurpose code in other themes. Few translations are needed here.
# http://codex.wordpress.org/I18n_for_WordPress_Developers
# http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
# http://markjaquith.wordpress.com/2011/10/06/translating-wordpress-plugins-and-themes-dont-get-clever/

# Actions to be run on the 'after_setup_theme' hook:
add_action('after_setup_theme', function() {
  \defined('WP_DEBUG') && WP_DEBUG ? \add_filter('@modes', function($arr) {
    return $arr ? \array_unique(!($arr[] = 'debug') ?: $arr) : array('debug');
  }) : remove_action('wp_head', 'wp_generator'); # tighten security

  add_editor_style(); # codex.wordpress.org/Function_Reference/add_editor_style
  add_theme_support('automatic-feed-links'); # required
  add_theme_support('post-thumbnails'); # "featured image"

  # http://codex.wordpress.org/Custom_Backgrounds
  # See _custom_background_cb in wp-includes/theme.php
  add_theme_support('custom-background', array(
    'wp-head-callback' => function() {
      $image = get_background_image();
      $image and $image = set_url_scheme($image);
      
      $style = !$image ? array() : \array_reduce(array(
        array('repeat', array('repeat', 'no-repeat', 'repeat-x', 'repeat-y'), 'repeat', ''),
        array('position_x', array('left', 'center', 'right'), 'position', 'top '),
        array('attachment', array('scroll', 'fixed'), 'attachment', '')
      ), function($image, $arr) {
        $option = \get_theme_mod('background_' . $arr[0]);
        $option = \in_array($option, $arr[1]) ? $option : $arr[1][0];
        $image[] = 'background-' . $arr[2] . ':' . $arr[3] . $option . ';';
        return $image;
      }, array('background-image' => "background-image:url('$image');"));
      
      $color = get_background_color();
      $color and $style[] = "background-color:#$color;";
      if ($style and $selector = apply_filters('@background_selector', '.custom-background')) {
        $style = $selector . '{' . \implode('', $style) . '}';
        $media = apply_filters('@background_media', 'screen');
        $media and $style = "@media $media{" . $style . '}';
        echo apply_filters('@background_style', "<style>$style</style>\n");
      }
    }
  ));
}, 0);

# Hooks to activate via corresponding template file:
\array_filter(array(
  '@header', '@footer', '@branding', '@main', '@loop', '@entry', '@comments'
  ), function($hook) {
  add_action("$hook.php", function($tagname = null) use ($hook) {
    $tagname && \is_string($tagname) or $tagname = 'div';
    echo \rtrim("<$tagname " . apply_filters($hook . '_atts', '')) . "><div class='container'>\n";
    do_action($hook);
    echo "\n</div></$tagname>\n\n";
  });
});

# Basic contextual support.
add_filter('body_class', function($arr) {
  global $wp_registered_sidebars;
  if (!empty($wp_registered_sidebars))
    foreach ($wp_registered_sidebars as $k => $v)
      is_active_sidebar($k) and $arr[] = "has-$k-widgets";
  foreach (\apply_filters('@modes', array()) as $v)
    \strlen($v = sanitize_key($v)) and $arr[] = 'mode-' . $v;
  return \array_unique(\array_merge(array(
    is_child_theme() ? 'child-theme' : 'parent-theme',
    is_singular() ? 'singular' : 'plural'
  ), $arr));
});

add_filter('@html_tag', function() {
  # Emulate language_attributes() b/c it has no "get" version.
  # Include its 'language_attributes' filter for plugin usage.
  $atts = 'dir="' . (is_rtl() ? 'rtl' : 'ltr') . '" lang="' . get_bloginfo('language') . '"';
  $atts = array(\trim(apply_filters('language_attributes', $atts)));
  $atts[] = 'id="start"';
  $class = get_body_class();
  \in_array('void-avatars', $class) and do_action('@void', '@comment_avatar');
  \in_array('void-thumbnails', $class) and do_action('@void', '@thumbnail');
  \in_array('void-excerpts', $class) and do_action('@void', 'the_excerpt');
  \array_unshift($class, 'no-js', 'custom');
  $class = \implode(' ', \array_unique($class));
  $atts[] = "class='$class'";
  $atts = \trim(apply_filters('@html_atts', \implode(' ', $atts)));
  return "<html $atts>\n";
}, 0);

#!
add_action('@void', function($hook, $fn = null, $at = null,  $use = null) {
  remove_all_filters($hook);
  add_filter($hook, $fn ?: '__return_null', null === $at ? 10 : $at, null === $use ? 1 : $use);
}, 0, 4);

add_filter('@body_tag', function() {
  $atts = \trim(apply_filters('@body_atts', ''));
  return $atts ? "<body $atts>\n" : '';
}, 0);

add_action('@body', apply_filters('@body_actions', function() {
  # http://github.com/ryanve/action/issues/4
  $skip = '<a class="assistive" href="#main">' . __('skip', 'theme') . '</a>';
  $skip = apply_filters('@skip_anchor', $skip);
  if ($skip = \trim(\strip_tags($skip, '<a>'))) echo "$skip\n\n";
  foreach (array(
    array(5, 'get_header'),
    array(10, function() { locate_template('main.php', true, false); }),
    array(30, 'get_footer')
  ) as $fn) has_action('@body', $fn[1]) or add_action('@body', $fn[1], $fn[0]);
}), 0);

# Early-priority init actions:
add_action('init', function() {
  # Define CSS (handle, uri, deps, ver, media)
  # http://github.com/ryanve/action/issues/2
  # http://github.com/ryanve/action/issues/5
  $index = trailingslashit(get_template_directory_uri());
  wp_register_style('parent-base', $index . 'base.css', array(), null, null);
  wp_register_style('parent-style', $index . 'style.css', array('parent-base'), null, 'screen,projection');
  is_child_theme() || is_admin() or wp_enqueue_style('parent-style');
}, 1);

# Frontend-only normal-priority init actions:
is_admin() or add_action('init', function() {
  # http://codex.wordpress.org/Migrating_Plugins_and_Themes_to_2.7/Enhanced_Comment_Display
  is_singular() and wp_enqueue_script('comment-reply');
});

# Register sidebars
add_action('widgets_init', function() {
  $areas = (array) apply_filters('@widget_areas', \array_map(function($id) {
    return array('id' => $id, 'name' => ".$id-widget-area"); # Correlate names to CSS selectors.
  }, array('header', 'major', 'minor', 'footer')));

  # Merge sensible defaults into codex.wordpress.org/Function_Reference/register_sidebar
  foreach ($areas as $a)
    $a and register_sidebar(\array_merge(array('before_widget' => '<li class="widget %2$s">'), $a));
});

# Display sidebars
add_action('get_sidebar', apply_filters('@sidebar_actions', function($id) {
  if (!\is_string($id) || !\strlen($id) || !is_active_sidebar($id)) return;
  if (locate_template(array("sidebar-$id.php"), false)) return;
  echo "<ul class='widget-area $id-widget-area'>";
  dynamic_sidebar($id);
  echo "</ul>\n\n";
}));

add_action('@header', function() {
  is_active_sidebar('header') and get_sidebar('header');
  locate_template('branding.php', true, false);
});

add_action('@footer', function() {
  is_active_sidebar('footer') and get_sidebar('footer');
});

# Favor classes over IDs.
add_filter('nav_menu_item_id', '__return_false');
add_filter('nav_menu_css_class', function($arr, $item = null) {
  if (\is_array($arr) && \is_object($item) && isset($item->ID))
    \in_array($item = 'menu-item-' . $item->ID, $arr) or $arr[] = $item;
  return $arr;
}, 10, 2);

add_action('@branding', function() {
  $name = get_bloginfo('name');
  $home = home_url();
  echo "<h1 class='site-name site-title'><a rel=home href='$home'><span>$name</span></a></h1>";
  echo apply_filters('@tagline', '');
});

add_action('@main', apply_filters('@main_actions', function() {
  is_active_sidebar('major') and get_sidebar('major');
  get_template_part('loop', is_singular() ? 'singular' : 'plural');
  is_active_sidebar('minor') and get_sidebar('minor');
}));

\array_filter(array('previous', 'next'), function($rel) {
  $hook = $rel . '_posts_link_attributes';
  $rel = \substr($rel, 0, 4);
  add_filter($hook, function($atts = '') use ($rel) {
    $atts or $atts = '';
    return \is_string($atts) ? \ltrim($atts . " rel='$rel' class='$rel'") : $atts;
  });
});

add_action('@loop', apply_filters('@loop_actions', function() {
  static $ran; 
  if ($ran = null !== $ran) return; # only run once
  
  is_singular() or add_action('@loop', function() {
    echo '<header class="loop-header">';
    do_action('@loop_header'); 
    echo "</header>\n\n";
  }, 5);

  add_action('@loop', function() {
    # The actual loop
    if (!have_posts()) locate_template('loop-empty.php', true, false);
    else for ($path = locate_template('entry.php', false); have_posts();) the_post() xor require $path;
  }, 10);

  add_action('@loop', function() {
    echo '<nav class="loop-nav arrestive">';
    do_action('@loop_nav'); 
    echo "</nav>\n\n";
  }, 20);
  
  add_action('@loop_nav', apply_filters('@loop_nav_actions', is_singular() ? function() {
    previous_post_link('%link');
    next_post_link('%link');
  } : function() {
    $prev = '<span>' . __('Prev', 'theme') . '</span>';
    $next = '<span>' . __('Next', 'theme') . '</span>';
    posts_nav_link(' ', $prev, $next);
  }));
}), 0);

add_action('@loop_header', function() {
  $data = array();

  if (is_category() || is_tag() || is_tax()) {
    $data['case'] = 'tax';
    $data['name'] = single_term_title('', false);
    $data['description'] = term_description('', get_query_var('taxonomy'));
  } elseif (is_author()) {
    $data = get_queried_object();
    $data = array('case' => 'user', 'name' => $data->display_name, 'description' => $data->user_description);
  } elseif (is_search()) {
    $data['case'] = 'search';
    $data['name'] = __('Search: ', 'theme') . get_search_query();
  } elseif (is_date()) {
    $data['case'] = 'date';
    $parts = \explode('/', \ltrim($_SERVER['REQUEST_URI'], '/'));
    $data['name'] = array();
    while (\is_numeric($n = \array_shift($parts)))
      $data['name'][] = $n;
    $data['name'] = \implode('-', $data['name']);
    $data['description'] = __('Archives.', 'theme');
  } elseif (is_post_type_archive()) {
    $data['name'] = post_type_archive_title('', false);
    if ($type = get_post_type_object(get_query_var('post_type')))
      $type->description and $data['description'] = $type->description;
  }

  if ($data = apply_filters('@loop_data', $data)) {
    $data = \array_replace(\array_fill_keys(array('name', 'image', 'case'), null), $data);
    $data['case'] = $data['case'] ? \preg_replace('#[^\w\s-]+#', '', $data['case']) : '';
    $data['class'] = 'loop ' . $data['case'] . ' ';
    $makeClass = function($prop) use ($data) {
      return \trim(\preg_replace('#\s+#', '-' . $prop . ' ', $data['class']));
    };

    ($markup = $data['image'] = $data['image'] ?: '')
      and \ctype_graph($src = \strip_tags($data['image']))
      and ($alt = \htmlentities(\strip_tags($data['name']), ENT_QUOTES, null, false))
      and $markup = '<img class="' . $makeClass('image') . "\" src='$src' alt='$alt'>";

    foreach (array('name' => 'h1', 'description' => 'div') as $prop => $tagname) {
      if (isset($data[$prop])) {
        $class = $makeClass($prop);
        $markup .= "<$tagname class='$class'>" . $data[$prop] . "</$tagname>";
      }
    }
    echo $markup;
  }
}, 5);

add_action('@entry', apply_filters('@entry_actions', function() {
  static $ran, $content_mode; 
  $content_mode = (bool) apply_filters('@content_mode', is_singular()); # dynamic between iterations
  if ($ran = null !== $ran) return; # only run once

  current_theme_supports('post-thumbnails') and add_filter('@thumbnail', function() use (&$content_mode) {
    if (!$content_mode and $size = apply_filters('@thumbnail_size', 'thumbnail'))
      if ($img = get_the_post_thumbnail(null, $size))
        return !($url = get_permalink()) || \strip_tags($img, '<img>') !== $img ? $img
          : "<a class='$size-anchor image-anchor' rel='bookmark' href='$url'>$img</a>";
  }, 0);

  add_action('@entry', function() use (&$content_mode) {
    $full = $content_mode;
    if ($full) echo '<header class="entry-header">';
    do_action('@entry_header'); 
    if ($full) echo "</header>\n\n";
  }, 5);

  add_action('@entry', function() use (&$content_mode) {
    echo $content_mode ? '<div class="entry-content">': '<div class="entry-summary">';
    $content_mode ? the_content() : the_excerpt();
    echo "</div>\n\n";
  }, 10);

  add_action('@entry', function() use (&$content_mode) {
    if (!$content_mode) return;
    echo '<footer class="entry-footer" role="contentinfo">';
    do_action('@entry_footer'); 
    echo "</footer>\n\n";
  }, 15);
    
  is_singular() and add_action('@entry', function() {
    comments_template('/comments.php', true);
  }, 20);
}), 0);

# Default attributes
# [id] is for jumps (not CSS)
\call_user_func(function() {
  foreach(array(
    '@header' => 'id="header" class="site-header" role="banner"',
    '@footer' => 'id="footer" class="site-footer"',
    '@branding' => 'class="site-branding"',
    '@main' => 'id="main" role="main"',
    '@loop' => 'class="loop hfeed"',
    '@entry' => function() {
      $class = \implode(' ', get_post_class());
      return $class ? "class='$class'" : '';
    },
    '@comments' => function() {
      $able = comments_open() ? 'open' : 'closed';
      $some = have_comments() ? 'has' : 'lacks';
      $used = 'open' == $able || 'has' == $some ? 'used' : 'unused';
      $atts = "class='comments comments-$able $some-comments $used'";
      return is_singular() ? "id='comments' $atts" : $atts;
    },
    '@comment' => function() {
      $id = get_comment_ID();
      $atts = is_singular() ? array("id='comment-$id'") : array(); 
      $class = \implode(' ', get_comment_class('', $id));
      $class and $atts[] = "class='$class'";
      return \implode(' ', $atts);
    }
  ) as $hook => $atts) add_filter($hook . '_atts', \is_scalar($atts) ? function() use ($atts) {
    return $atts;
  } : $atts, 0);
});

add_filter('post_class', function($arr = array()) {
  # This compares using the format from: Settings > General > Date Format
  # Maybe we should use Y-m-d or provide a filter like:
  # $format = apply_filters('@time_compare_format', '');
  $arr = (array) $arr;
  $arr[] = get_the_date() === get_the_modified_date() ? 'unrevised' : 'revised';
  $arr[] = has_post_thumbnail() ? 'has-thumbnail' : 'lacks-thumbnail';
  return \array_unique($arr);
});

add_action('@entry_header', function() {
  echo apply_filters('@thumbnail', null);
  $markup = '<h1 class="entry-title">';
  $markup .= '<a rel="bookmark" href="' . get_permalink() . '">';
  $markup .= '<span class="headline">' . get_the_title() . '</span></a></h1>';
  echo apply_filters('@headline', $markup);
}, 5);

add_filter('@entry_meta_groups', function($arr) {
  if (false === $arr) return array();
  $all = \array_fill_keys(array('author', 'time', 'pages', 'tax'), true);
  $arr = \is_array($arr) ? \array_replace($all, \array_intersect_key($arr, $all)) : $all;
  true === $arr['time'] and $arr['time'] = array('published', 'modified');
  $all = get_taxonomies();
  $arr['tax'] and $arr['tax'] = true === $arr['tax'] ? $all : \array_intersect_key($all, $arr['tax']);
  return $arr;
}, 100);

add_action('@entry_header', function() {
  echo apply_filters('@entry_meta', '', '@entry_header');
}, 7);

add_action('@entry_footer', function() {
  echo apply_filters('@entry_meta', '', '@entry_footer');
}, 20);

add_filter('@entry_meta_groups', function($groups, $hook) {
  return '@entry_footer' === $hook;
}, 0, 2);

add_filter('@entry_meta', function($markup, $hook) {
  $items = array();
  $groups = \array_filter(apply_filters('@entry_meta_groups', array(), $hook) ?: array());
  foreach ($groups as $name => $group) {
    $group = \is_array($group) ? \array_diff($group, array(null)) : array(null);
    $defaults = array('label' => $name, 'value' => null, 'sep' => null);
    foreach ($group as $case) {
      foreach (array('label', 'value') as $k)
        $defaults[$k . 'Atts'] = "class='meta-$k $name-$k $name-$case'";
      $data = apply_filters("@entry_meta:$name", null, $case);
      if ($data && \is_array($data)) {
        $data = \array_replace($defaults, \array_intersect_key($data, $defaults));
        \extract($data);
        if ($value) {
          $value = (array) $value;
          $dt = "<dt $labelAtts>";
          $dd = "<dd $valueAtts>";
          $value = $dd . \implode(null === $sep ? "</dd>$dd" : $sep, $value) . '</dd>';
          $items[\is_string($case) ? $case : $name] = $dt . $label . '</dt>' . $value;
        }
      }
    }
  }
  $items = \apply_filters('@entry_meta_items', $items, $groups);
  $void = $items ? '' : ' void';
  return "<dl class='pairs meta-list entry-meta$void'>" . \implode('', $items) . '</dl>';
}, 0, 2);

# http://microformats.org/wiki/hcard
add_filter('@entry_meta:author', function() {
  global $authordata;
  $a = array();
  if (\is_object($authordata) and $href = get_author_posts_url($authordata->ID)) {
    $name = get_the_author();
    $a['value'] = "<span class='vcard'><a class='url fn n' href='$href' rel='author'>$name</a></span>";
    $a['label'] = __('By', 'theme');
  }
  return $a;
}, 0);

add_filter('@entry_meta:time', function($void, $case) {
  $case = \array_search($case, array('modified', 'published'), true);
  if ($case || 0 === $case) {
    $fn = $case ? 'get_the_date' : 'get_the_modified_date';
    $rel = $case ? 'index' : '';
    $label = $case ? 'Posted' : 'Updated';
    $class = $case ? 'published' : 'updated';
    $date = \call_user_func($fn); # Settings > General > Date Format
    $w3c = \call_user_func($fn, DATE_W3C); # PHP DateTime
    $idx = get_year_link(\strtok($w3c, '-'));
    $rel and $rel = " rel='$rel'";
    $date = "<a$rel href='$idx'>$date</a>";
    return array('label' => $label, 'value' => "<time class='$class' datetime='$w3c'>$date</time>");
  }
}, 0, 2);

add_filter('@entry_meta:pages', function() {
  $pages = \trim(wp_link_pages(array('echo' => 0, 'before' => ' ', 'after'  => ' ')));
  return \strlen($pages) ? array('label' => __('Pages', 'theme'), 'value' => $pages) : array();
}, 0);

add_filter('@entry_meta:tax', function($void, $name) {
  if ($tax = get_taxonomy($name)) {
    $id = get_the_ID();
    $type  = get_post_type($id);
    $lists = array();
    $sep = '<<<!>>>';
    if (is_object_in_taxonomy($type, $name) and $label = \trim($tax->label)) {
      $data = array('label' => $label , 'types' => array('tax', $label), 'sep' => ', ');
      $data['value'] = \array_filter(\explode($sep, get_the_term_list($id, $name, '', $sep, '')));
      $class = sanitize_html_class(\mb_strtolower($label));
      foreach (array('label', 'value') as $k)
        $data[$k . 'Atts'] = $class ? "class='meta-$k tax-$k $class-$k'" : "class='meta-$k tax-$k";
      return $data;
    }
  }
}, 0, 2);

# Clean excerpt whitespace
add_filter('the_excerpt', 'normalize_whitespace'); # wp
add_filter('the_excerpt', 'trim');

is_admin() or add_action('template_redirect', function() {
  # Remove problematic CPT archive actions.
  if (!is_post_type_archive()) return;
  remove_action('wp_head', 'feed_links_extra', 3);
  $type = $var = get_query_var('post_type');
  $slug = \basename($_SERVER['REQUEST_URI']);
  \is_array($var) and $type = \in_array($slug, $var) ? $slug : \array_shift($var);
  $type === $var or set_query_var('post_type', $type);
});

# Urgent <head> actions:
# meta[charset]
add_action('wp_head', function() {
  $tag = '<meta charset="utf-8">';
  echo \ltrim(apply_filters('@meta_charset', $tag) . "\n");
}, -5); 

# [dns-prefetch] markup
add_action('wp_head', function() {
  $uris = (array) apply_filters('@dns_prefetches', array());
  echo \implode('', \array_reduce($uris, function($result, $uri) {
    \ctype_graph($uri)
      and ($uri = \explode('//', $uri))
      and isset($uri[1]) # only deal with full uris
      and ($uri = \strtok($uri[1], '/')) # strip to authority
      and (false === \strpos($uri, $_SERVER['SERVER_NAME'])) # don't prefetch self
      and !\preg_match('#[^\w@:_.-]#', $uri) # ensure remaining uri is entirely safe
      and $result[$uri] = "<link rel='dns-prefetch' href='//$uri'>\n";
    return $result;
  }, array()));
}, -4); 

# [dns-prefetch] uris
add_filter('@dns_prefetches', function($uris) {
  global $wp_scripts, $wp_styles;
  foreach (array($wp_scripts, $wp_styles) as $o) \is_object($o)
    and !empty($o->queue)
    and !empty($o->registered)
    and $uris = \array_merge($uris, \array_intersect_key(
      (array) \wp_list_pluck($o->registered, 'src'),
      \array_flip(\array_values((array) $o->queue))
    ));
  return $uris;
});

# <title>
add_action('wp_head', function() {
  $tag = \trim(is_front_page() ? get_bloginfo('name') : wp_title('', false));
  if ($tag === '') $tag = $_SERVER['REQUEST_URI'];
  if ($tag = \trim(apply_filters('@title_tag', "<title>$tag</title>"))) echo "$tag\n";
}, -3);

# <meta>
add_action('wp_head', function() {
  foreach (apply_filters('@meta', array(
    'viewport' => array('name' => 'viewport', 'content' => 'width=device-width,initial-scale=1.0')
  )) as $tag) {
    if (\is_string($tag)) {
      $tag = \strip_tags($tag, '<meta>');
    } elseif (\is_array($tag) and $tag = \array_diff($tag, array(null, false))) {
      $atts = array();
      foreach ($tag as $k => $v)
        ($v = $v ? true === $v ? '' : esc_attr($v) : $v) xor $atts[] = "$k='$v'";
      $atts = \implode(' ', $atts);
      $tag = $atts ? "<meta $atts>" : '';
    } else continue;
    echo \ltrim("$tag\n");
  }
}, -1); 

# Remove WP gallery <style> tags. (wp-includes/media.php)
add_filter('use_default_gallery_style', '__return_false');

# Remove WP embedded .recentcomments style. (wp-includes/default-widgets.php)
add_filter('show_recent_comments_widget_style', '__return_false');

# Comments logic adapted from http://bit.ly/github-twentytwelve
add_action('@comments', apply_filters('@comments_actions', function() {
  static $ran; 
  if ($ran = null !== $ran) return; # only run once
  if (post_password_required() || !post_type_supports(get_post_type(), 'comments')) return;

  $have = have_comments();
  $have and add_action('@comments', function() {
    echo '<h2 class="loop-title comments-title">' . comments_number() . '</h2>';
  }, 5);
  
  $have and add_action('@comments', function() {
    # http://microformats.org/wiki/xoxo
    echo '<ol class="xoxo comments clearfix">';
    wp_list_comments(apply_filters('@list_comments', array(
      'style' => 'ol',
      'avatar_size' => 60 ,
      'callback' => function($comment, $arr, $depth) {
        $GLOBALS['comment'] = $comment;
        $GLOBALS['comment_depth'] = $depth;
        $atts = \rtrim(' ' . apply_filters('@comment_atts', ''));
        echo "<li><article$atts>"; 
        do_action('@comment');
        echo '</article>'; 
      }
    )));
    echo '</ol>';
  }, 10);
  
  $have and 1 < get_comment_pages_count() and get_option('page_comments') and add_action('@comments', function() {
    echo '<nav><h3 class="assistive">' . __('Comment navigation', 'theme') . '</h3>';
    previous_comments_link(apply_filters('@comments_older', __('&laquo; Older', 'theme')));
    next_comments_link(apply_filters('@comments_newer', __('Newer &raquo;', 'theme')));
    echo '</nav>';
  }, 15);
  
  add_action('@comments', function() {
    if (comments_open()) comment_form(apply_filters('@comment_form', array()));
    else echo '<p class="status">' . __('Comments are closed.', 'theme') . '</p>';
  }, 20);
}), 0);

add_action('@comment', apply_filters('@comment_actions', function() {
  static $ran; 
  if ($ran = null !== $ran) return; # only run once

  add_action('@comment', function() {
    global $comment;
    $avatar = apply_filters('@comment_avatar', get_avatar($comment, 90));
    $avatar = \strip_tags(\trim($avatar), '<img>');
    $href = get_comment_author_url();
    $href = $href ? ' href="' . $href . '"' : '';
    $avatar = "<a$href class='avatar-anchor image-anchor'>$avatar</a>";
    $markup = '<header class="comment-header">' . $avatar;
    $markup .= '<div class="comment-meta">'; 
    $markup .= '<h3 class="meta-value">' . get_comment_author_link() . '</h3>';
    $markup .= '<h4 class="meta-value time-value time-published">';
    $markup .= get_comment_date() . '</h4></div></header>';
    echo apply_filters('@comment_header', $markup);
  }, 5);
  
  add_action('@comment', function() {
    echo '<div class="comment-content">' . get_comment_text(get_comment_ID()) . '</div>';
  }, 10);
  
  add_action('@comment', function() {
    global $comment;
    if (empty($comment->comment_approved)) {
      $markup = __('Your comment is awaiting moderation.', 'theme');
      $markup = "<p class='alert' role='alert'>$markup</p>";
      echo apply_filters('@comment_moderation', $markup);
    }
  }, 10);
}), 0);

# Use .assistive rather than declaring duplicate rules for .screen-reader-text
add_filter('get_search_form', function($markup) {
  return $markup ? \str_replace('screen-reader-text', 'assistive', $markup) : $markup;
});

add_filter('@output', function($html) {
  # Remove excess whitespace to ease readability:
  return \preg_replace('/\n+\s*\n+/', "\n\n", \trim($html));
});