<?php
namespace theme;

/**
 * @requires PHP 5.3+
 * @license  MIT
 * @author   Ryan Van Etten <@ryanve>
 * @link     github.com/ryanve/action
 *
 * Hooks created by the theme are prefixed with the '@' symbol 
 * as not to conflict with hooks created by the WordPress core.
 * Lines marked with '#wp' use non-obvious WordPress functions. 
 *
 * @link  codex.wordpress.org/Function_Reference/add_action
 * @link  codex.wordpress.org/Function_Reference/add_filter
 * @link  codex.wordpress.org/Plugin_API/Action_Reference
 */

  
# Load Hybrid Core ( themehybrid.com/hybrid-core/setup ) if present.
if ( \file_exists( __DIR__ . '/hybrid-core/hybrid.php' ) ) {
     # require_once( __DIR__ . '/hybrid-core/hybrid.php' );
     # \class_exists('\Hybrid') and $hybrid = new \Hybrid();
}

# Basic functions for working in namespaces:

/**
 *
 */
if ( ! \function_exists( __NAMESPACE__ . '\\ns' ) ) {
    function ns ( $name ) {
        return __NAMESPACE__ . '\\' . ltrim( $name, '\\' );
    }
}
 
/**
 *
 */
if ( ! \function_exists( __NAMESPACE__ . '\\exists' ) ) {
    function exists ( $name, $what = 'function' ) {
        return \call_user_func( $what . '_exists', ns($name) );
    }
}

/**
 * Call a namespaced function by name. ( Params can be supplied via array. )
 * @param   string    $fname
 * @param   array     $params
 */
if ( ! exists( 'apply' ) ) {
    function apply ( $fname, $params = array() ) {
        return \call_user_func_array( ns( $fname ), $params );
    }
}

/**
 * Get or set arbitrary data.
 */
if ( ! exists( 'data' ) ) {
    function data ( $key = null, $value = null ) {

        static $hash;  # php.net/manual/en/language.variables.scope.php
        isset( $hash ) or $hash = array();
        
        if ( \func_num_args() > 1 )
            return $hash[ $key ] = $value; # set

        if ( \is_scalar($key) ) 
            return $hash[ $key ];          # get
            
        if ( null === $key )
            return $hash;                  # get all
            
        foreach ( $key as $k => $v )       # set multi
            $hash[ $k ] = $v;
        return $hash; 
    }
}

/**
 *
 */
if ( ! exists( 'data_e' ) ) {
    function data_e () {
        echo apply( 'data', func_get_args() );
    }
}

# Set a default textdomain for use below.
data( 'textdomain', get_template() ); #wp
    
# wrap the translate functions w/in the theme namespace so
# the $textdomain param is automatically added if omitted.
# @link  codex.wordpress.org/I18n_for_WordPress_Developers

if ( ! exists( '__' ) ) {
    function __ ( $text = '', $textdomain = null ) {
        return \__( $text, null === $textdomain ? data( 'textdomain' ) : $textdomain ); #wp 
    }
}

if ( ! exists( '_e' ) ) {
    function _e ( $text = '', $textdomain = null ) {
        echo __ ( $text, $textdomain );
    }
}


# I'm going to port this to a plugin
# @link  codex.wordpress.org/Conditional_Tags
add_action( 'wp', function () {
    data( 'context', call_user_func(function () {

        # Adapted from Hybrid Core
        # github.com/justintadlock/hybrid-core -> functions -> context.php

        $array = array( is_child_theme() ? 'child-theme' : 'parent-theme' );
        is_front_page() and $array[] = 'home';
        is_paged()      and $array[] = 'paged';
        is_singular()   and $array[] = 'singular';
        
        if ( is_user_logged_in() ) {
            $array[] = 'logged-in';
            is_admin_bar_showing() and $array[] = 'admin-bar';
        } else {
            $array[] = 'logged-out';
        }
        
        if ( is_multisite() ) {
            $array[] = 'multisite';
            $array[] = 'blog-' . get_current_blog_id();
        }

        if ( is_home() ) {
            $array[] = 'blog';
            return $array;
        }

        $object = get_queried_object();
        $id     = get_queried_object_id();
        $type   = null;
        $format = null;

        if ( is_singular() ) {

            $type = $object->post_type;
            $array[] = 'singular';
            $array[] = 'singular-' . $type;
            $array[] = 'singular-' . $type . '-' . $id;
            
            if ( current_theme_supports( 'post-formats' ) && post_type_supports( $type, 'post-formats' ) ) {
                $format = get_post_format($id);
                $format && !is_wp_error($format) or $format = 'standard';
                $array[] = $type . '-format-' . $format;
            }
            
            if ( is_attachment() ) {
                $array[] = 'attachment';
                foreach ( \explode( '/', (string) get_post_mime_type() ) as $type )
                    $type and $array[] = 'attachment-' . $type;
            }

        } elseif ( is_search() ) {
            $array[] = 'search';

        } elseif ( is_404() ) {
            $array[] = 'error-404';

        } elseif ( is_archive() ) {
        
            $array[] = 'archive';

            if ( is_tax() || is_tag() || is_category() ) {
                $type = $object->taxonomy;
                $array[] = 'taxonomy';
                $array[] = 'taxonomy' . $type;
                $array[] = 'taxonomy' . $type . sanitize_html_class( $object->slug, $object->term_id );
                
            } elseif ( is_post_type_archive() ) {
                $type = get_post_type_object( get_query_var('post_type') );
                $type and $array[] = 'archive-' . $type->name;
                
            } elseif ( is_author() ) {
                $array[] = 'user';
                $array[] = 'user-' . sanitize_html_class( get_the_author_meta('user_nicename', $id), $id );
                
            } else {
                if ( is_date() and $array[] = 'date' ) {
                    is_year()  and $array[] = 'year';
                    is_month() and $array[] = 'month';
                    is_day()   and $array[] = 'day';
                }
                if ( is_time() and $array[] = 'time' ) {
                    get_query_var('hour')   and $array[] = 'hour';
                    get_query_var('minute') and $array[] = 'minute';
                }
            }
        }

        return array_unique( $array );

    }));
});

# Actions to be run on the 'after_setup_theme' hook:
add_action('after_setup_theme', function () {
    remove_action( 'wp_head', 'wp_generator' ); # better security w/o this
});

# Default sidebars (1)
//add_action( 'widgets_init', 'register_sidebar', 10, 0 );
//add_action( 'widgets_init', 'register_sidebars', 10, 0 );

# still testing this
add_action('@after_header', function () {
    wp_nav_menu();
});

# still testing this
if ( ! is_child_theme() ) {

    add_action( 'widgets_init', 'register_sidebar', 10, 0 );
    
    add_action( 'init', function () {
        
        register_nav_menus( array('primary' => 'Primary') );
    
    });
}

# Insert the loop into [role="main"]
add_action ('@main', function () {
    # codex.wordpress.org/Function_Reference/get_template_part
    get_template_part( 'loop', is_singular() ? 'singular' : 'index' ); #wp
});

/*add_filter ('the_content', function ( $html ) {
    $html and $html .= wp_link_pages( array('echo' => 0) );
    return $html;
}, 20 );*/

# Remove version from URI query strings to improve caching.
call_user_func(function ( $unversion ) {
    // add_filter( 'style_loader_src', $unversion );
    // is_admin() or add_filter( 'script_loader_src', $unversion );
}, function ( $src ) {
    # codex.wordpress.org/Function_Reference/remove_query_arg
    return remove_query_arg('ver', $src); #wp
});

# hmm maybe better to store in data()
add_filter( '_gaq', function () {
    return '[[]]';
});

# Actions to be run on the 'init' hook:
add_action( 'init', function () {

    # CPTs and taxonomies should register on init.
    # Scripts/styles should register/enqueue on init.

	if ( is_admin() ) { # Admin-specific actions
    
    } else { # Frontend-specific actions
    
        wp_enqueue_style( 'style', get_stylesheet_uri(), array(), null, null );
        $modernizr_uri = apply_filters('@modernizr_uri', 'http://airve.github.com/js/modernizr/modernizr_shiv.min.js');
        empty( $modernizr_uri ) or wp_enqueue_script( 'modernizr', $modernizr_uri, array(), null, false );
    
        // Google Analytics
        $gaq = apply_filters( '@gaq', array() );
        if ( !empty($gaq) ) {
            // WP runs json_encode on data provided to wp_localize_script
            // so decode it if it looks like it's already encoded.
            is_scalar($gaq) and $gaq = json_decode( $gaq );
            $ga_uri = apply_filters( '@ga_uri', 'http://www.google-analytics.com/ga.js' );
            empty( $ga_uri ) or wp_enqueue_script( 'ga', $ga_uri, array(), null, true );
            wp_localize_script( 'ga', '_gaq', $gaq );
        }
    }

});

# experimental
add_filter( 'style_loader_tag', function ( $html, $handle ) {
    
    $source = new \DOMDocument;
    $source->loadHtml($html);
    $node = $source->getElementsByTagName('link')->item(0);
    
    if ( $node->getAttribute('rel') !== 'stylesheet' )
        return $html;

    $attrs = apply_filters( '@stylesheet_attributes', array(), $handle );
    if ( !is_array($attrs) )
        return $html;
    unset ($attrs['rel']);

    # remove uneeded defaults
    foreach ( array('media' => 'all', 'type' => 'text/css') as $k => $v )
        empty($attrs[$k]) && $node->getAttribute($k) === $v and $node->removeAttribute($k);

    # add custom attrs
    foreach ( $attrs as $k => $v )
        empty($v) or ( is_int($k) ? $node->getAttribute($v, '') : $node->getAttribute($k, $v) );

    $output = new \DOMDocument;        
    $output->appendChild( $output->importNode($node, true) );    
    return $output->saveHtml();

}, 12, 2);

add_filter( '@output', function ( $html ) {

    # The '@output' filter is mainly designed for use 
    # with the PHP DOMDocument interface, but I didn't 
    # use DOMDocument in the default filters in case
    # of lack of support or invalid markup.
    # @link php.net/manual/en/class.domdocument.php

    # remove excessive whitespace for better readability
    $html = \preg_replace( '/\n+\s*\n+/', "\n\n", $html );

    return $html;

});

add_filter ('@html_tag', function ( $html ) {
    // $node->setAttribute('dir', is_rtl() ? 'rtl' : 'ltr'); #wp
    return '<html class="g">';
});

# testing ( not in use )
add_action ('$script', function ($node) {
    $node->setAttribute('data-yea', 'aaaaa');
    $node->removeAttribute('type');
});

# early priority <head> actions
# debating whether to use filters (like below) and/or to make
# them named functions so child themes can use remove_action
add_action ('wp_head', function () {
     $tag = '<meta charset="utf-8">';
     echo ltrim( apply_filters( '@meta_charset', $tag ) . "\n" );
}, -5 ); 

add_action ('wp_head', function () {
    $tag = '<title>' . get_the_title() . '</title>';
    echo ltrim( apply_filters( '@title_tag', $tag ) . "\n\n" );
}, -3 ); 

add_action ('wp_head', function () {
    $tag = '<meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo ltrim( apply_filters( '@meta_viewport', $tag ) . "\n" );
}, -1 ); 


# comments callback ( see comments.php )
# codex.wordpress.org/Function_Reference/wp_list_comments
add_filter('@list_comments', function ( $array ) {
    null === $array['callback'] and $array['callback'] = function ( $comment, $array, $depth ) {
        $GLOBALS['comment'] = $comment;
        $GLOBALS['comment_depth'] = $depth;
        $comment_type = get_comment_type($comment->comment_ID); #wp-includes/comment-template.php
        locate_template( array( 'comment-' . $comment_type . '.php', 'comment.php' ), true, false ); #wp')
    };
    return $array;
});


add_filter('the_author_posts_link', function ( $tag ) {
    # add hcard classes to the link if there's not already any classes
    if ( false !== strpos( $tag, 'class=' ) )
        return $tag;
    return str_replace( '<a href', '<a class="url fn n" href', $tag );
});





