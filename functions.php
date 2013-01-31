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
    require_once( __DIR__ . '/hybrid-core/hybrid.php' );
    \class_exists('\Hybrid') and $hybrid = new \Hybrid();
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

# Basic contextual support.
add_filter('body_class', function ($array) {
    return \array_unique( \array_merge($array, array(
        is_child_theme() ? 'child-theme' : 'parent-theme'
      , is_singular() ? 'singular' : 'plural'
    )));
});

# Actions to be run on the 'after_setup_theme' hook:
add_action('after_setup_theme', function () {
    remove_action( 'wp_head', 'wp_generator' ); # better security w/o this
});

add_action( '@header', function () {
    locate_template( 'branding.php', true, false );
}, apply_filters('@branding_priority', 10) );

# still testing this
add_action('@header', function () {
    echo apply_filters('@menu', \str_repeat( ' ', 8 )
      . '<nav id="menu" role="navigation">'
      . '<h2 class="assistive menu-toggle">Menu</h2>'
      . wp_nav_menu(array(
            'theme_location' => 'menu'
          , 'container'      => false
          , 'echo'           => false
          , 'menu_class'     => 'nav'
          , 'items_wrap'     => '<ul class="%2$s">'
                . '<li class="assistive"><a href="#main" accesskey="5">Skip</a></li>%3$s</ul>'
    )) . '</nav>' . "\n\n");
}, apply_filters('@menu_priority', 10));

add_action('@header', function () {
    is_active_sidebar('header') and get_sidebar('header');
});

add_action('@footer', function () {
    is_active_sidebar('footer') and get_sidebar('footer');
});

# still testing this
#if ( ! is_child_theme() ) {
    add_action( 'widgets_init', function () {
        register_sidebar(array( 
            'name' => __( 'Sidebar' )
          , 'id' => 'sidebar-1' 
          , 'description' => __( 'Inserts into #sidebar' )
        ));
        register_sidebar(array( 
            'name' => __( 'Header' )
          , 'id' => 'header' 
          , 'description' => __( 'Inserts into #header' )
        ));
        register_sidebar(array( 
            'name' => __( 'Footer' )
          , 'id' => 'footer' 
          , 'description' => __( 'Inserts into #footer' )
        ));
    });
    add_action( 'init', function () {
        register_nav_menus( array('menu' => 'Menu') );
    });
#}

add_action('@main', function () {
    # insert the loop into [role="main"]
    # codex.wordpress.org/Function_Reference/get_template_part
    get_template_part( 'loop', is_singular() ? 'singular' : 'index' ); #wp
}, apply_filters('@loop_priority', 10));

add_action('@loop', function () {
    # codex.wordpress.org/Function_Reference/locate_template
    is_singular() or locate_template( 'loop-header.php', true, false );
}, 1);

add_action('@loop', function () {
    # the actual loop
    if ( ! have_posts() )
        locate_template( 'loop-empty.php', true, false );
    else for ( $path = locate_template( 'entry.php', false, false ); have_posts(); ) {
        the_post();
        include( $path );
    }
});

add_action('@loop', function () {
    # codex.wordpress.org/Function_Reference/locate_template
    locate_template( 'loop-nav.php', true, false );
}, 20);

add_action('@entry', ns('entry_actions'), 0);
function entry_actions () {

    remove_action('@entry', __FUNCTION__, 0); # only run actions once
    static $ran; # redundancy to prevent it being called twice by other means
    if ( $ran = null !== $ran ) return;

    add_action('@entry', function () {# insert entry-header.php
        include ( locate_template( 'entry-header.php', false, false ) );
    }, 5);
    
    # allow the '@content_mode' to be changed between iterations
    # truthy => content | falsey => excerpt
    $content_mode = apply_filters( '@content_mode', is_singular() );

    add_action('@entry', $content_mode ? function () {
        include ( locate_template( 'entry-content.php', false, false ) );
    } : function () {
        static $summ; # cache template location b/c repeated calls are likely here
        include ( $summ = $summ ? $summ : locate_template( 'entry-summary.php', false, false ) );
    }, 10);

    $content_mode and add_action('@entry', function () {
        include ( locate_template( 'entry-footer.php', false, false ) );
    }, 15);
    
    $content_mode && is_singular() and add_action('@entry', function () {
        # codex.wordpress.org/Function_Reference/comments_template
        comments_template( '/comments.php', true );
    }, 20);
}

add_action('@entry_header', function () {
    $markup = '<h1 class="entry-title">';
    $markup .= '<a itemprop="url headline name" rel="bookmark" href="' . get_permalink() . '">';
    $markup .= get_the_title() . '</a></h1>';
    echo apply_filters( '@headline', $markup );
}, 5);

add_action('@entry_header', function () {

    $markup = '<dl class="byline meta-list">';
    
    global $authordata;
	\is_object( $authordata )
        and ( $link = get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ) # href
        and ( $link = "<a href='$link' class='url fn n' itemprop='author' rel='author'>" . get_the_author() .'</a>' )
        and ( $link = apply_filters( 'the_author_posts_link', $link ) ) #wp: the_author_posts_link()
        and $markup .= '<dt class="author-label">' . __('By') . '</dt><dd class="author-value vcard">' . $link . '</dd>';
        
    $times = array(
        'Published' => array( 'fn' => 'get_the_date', 'class' => 'published', 'rel' => 'index' )
      , 'Modified' => array( 'fn' => 'get_the_modified_date', 'class' => 'updated', 'rel' => null )
    );

    foreach ( $times as $k => $v ) {
        \extract($v);
        $date = call_user_func( $fn ); # Uses: Settings > General > Date Format
        $ymd = call_user_func( $fn, 'Y-m-d' );
        $idx = get_year_link( $y );
        $rel and $rel = ' rel="index"';
        $date = "<a$rel href='$idx'>$date</a>";
        $tag = "<time itemprop='date$k' class='$class' datetime='$ymd'>$date</time>";
        $k = \strtolower( $k );
        $tag = apply_filters( '@' . $k . '_tag', $tag, $date );
        $label = __( \ucfirst( $class ) );
        $markup .= '<dt class="' . $k . '-label time-label">' . $label . '</dt>';
        $markup .= '<dd class="' . $k . '-value time-value">' . $tag . '</dd>'; # maybe should filter here too
    }

    $markup .= '</dl>';
    echo apply_filters( '@byline', $markup );

}, 7);

add_action ('@entry_footer', function () {

    global $wp_taxonomies;
    static $taxos;

    echo apply_filters('@entry_pages', wp_link_pages(array(
        'echo'   => 0
      , 'before' => '<nav class="entry-pages meta-list"><h4 class="meta-label pages-label">' 
                    . __('Pages') . '</h4><div class="meta-value pages-value">'
      , 'after'  => '</div></nav>'
    )));

    isset( $taxos ) or $taxos = \wp_list_pluck( $wp_taxonomies, 'label' );
    $id    = get_the_ID();
    $type  = get_post_type( $id );   
    $markup = '';

    foreach ( $taxos as $name => $label ) {
        if ( is_object_in_taxonomy($type, $name) ) {
            if ( $class = sanitize_html_class( \mb_strtolower($label) ) ) {
                $terms = get_the_term_list( $id, $name, '<li>', '</li><li>', '</li>' );
                $void = $terms ? '' : ' void';
                $markup .= "<dt class='meta-label taxo-label $class-label$void'>$label</dt>";
                $markup .= "<dd class='meta-value taxo-value $class-value$void'>";
                $markup .= '<ul class="term-list">' . $terms . '</ul></dd>';
            }
        }
    }
    $markup = '<dl class="meta-list entry-taxos">' . $markup . '</dl>';

    $markup = apply_filters( '@entry_terms', $markup, $taxos );
    echo $markup;

}, 20);

add_action ('@entry', function () {
    # codex.wordpress.org/Function_Reference/comments_templatey
    is_singular() and comments_template( '/comments.php', true );
}, 30);

# Remove version from URI query strings to improve caching.
\call_user_func(function ( $unversion ) {
    // add_filter( 'style_loader_src', $unversion );
    // is_admin() or add_filter( 'script_loader_src', $unversion );
}, function ( $src ) {
    # codex.wordpress.org/Function_Reference/remove_query_arg
    return remove_query_arg('ver', $src); #wp
});

add_action('@after_footer', function () {
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>

    <div class="diagnostic">
        <h3>Testing</h3>
        <ul>
            <li><a accesskey="x" rel="nofollow" href="http://html5.validator.nu/?doc=<?php echo $url; ?>">validate</a></li>
            <li><a accesskey="o" rel="nofollow" href="http://gsnedders.html5.org/outliner/process.py?url=<?php echo $url; ?>">outline</a></li>
            <li><a accesskey="d" rel="nofollow" href="http://www.google.com/webmasters/tools/richsnippets?url=<?php echo $url; ?>">data</a></li>
        </ul>
    </div>

<?php });

# Actions to be run on the 'init' hook:
add_action( 'init', function () {

    # CPTs and taxonomies should register on init.
    # Scripts/styles should register/enqueue on init.
    
    # Register Modernizr
    $modernizr_uri = apply_filters( '@modernizr_uri', 'http://airve.github.com/js/modernizr/modernizr_shiv.min.js' );
    $modernizr_uri and wp_register_script( 'modernizr', $modernizr_uri, array(), null, false );

	if ( is_admin() ) { # Admin-specific actions
    
    } else { # Frontend-specific actions
    
        # Enqueue style.css
        wp_enqueue_style( 'style', get_stylesheet_uri(), array(), null, null );
        
        # Enqueue Modernizr
        $modernizr_uri and wp_enqueue_script( 'modernizr' );
    
        # Google Analytics
        if ( $gaq = apply_filters( '@gaq', array() ) ) {
            # WP runs json_encode on data provided to wp_localize_script
            # so decode it if it looks like it's already encoded.
            \is_scalar($gaq) and $gaq = \json_decode( $gaq );
            $ga_uri = apply_filters( '@ga_uri', 'http://www.google-analytics.com/ga.js' );
            $ga_uri and wp_enqueue_script( 'ga', $ga_uri, array(), null, true );
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
# wp-includes/comment-template.php
add_filter('@list_comments', function ( $arr ) {
    null === $arr['callback'] and $arr['callback'] = function ( $comment, $arr, $depth ) {
        $GLOBALS['comment'] = $comment;
        $GLOBALS['comment_depth'] = $depth;
        $attrs;
        $attrs = apply_filters( '@comment_attrs', $attrs );
        echo "<li><article $attrs>"; 
        do_action( '@comment' );
        echo '</article>'; 
    };
    return $arr;
});

add_filter('@comment_attrs', function () {
    if ( \function_exists('\\phat\\attrs') ) {
        # core.trac.wordpress.org/ticket/23236
        $attrs = array(); 
        $id = get_comment_ID();
        $attrs['id'] = 'comment-' . $id;
        $attrs['class'] = \implode( ' ', get_comment_class( '', $id ) );
        if ( 'comment' === get_comment_type($id) ) {
            $attrs['itemprop'] = 'comment';
            $attrs['itemscope'] = '';
            $attrs['itemtype'] = 'http://schema.org/UserComments';
        }
        return  \phat\attrs( $attrs );
    }
    return '';
}, 1);


add_filter('@comment', function () {
    global $comment;
    $markup = '<header class="comment-header">';
    $markup .= apply_filters( '@comment_avatar', get_avatar( $comment, 60 ) );
    $markup .= '<dl class="meta-list">'; 
    $markup .= '<dt>' . __('By') . '</dt>'; 
    $markup .= '<dd>' . get_comment_author_link() . '</dd>';
    $markup .= '<dt class="published-label">' . __('Published') . '</dt>';
    $markup .= '<dd class="published-value">' . get_comment_date() . '</dd>';
    $markup .= '</dl></header>';
    $markup .= '<div class="comment-content">';
    $comment->comment_approved or $markup .= apply_filters( '@comment_moderation', 
        '<p class="alert moderation">' . __( 'Your comment is awaiting moderation.' ) . '</p>' );
    $markup .= get_comment_text( $comment->comment_ID );
    $markup .= '</div>';
    echo $markup;
});


add_filter('the_author_posts_link', function ( $tag ) {
    # add hcard classes to the link if there's not already any classes
    if ( false !== strpos( $tag, 'class=' ) )
        return $tag;
    return str_replace( ' href=', ' class="url fn n" href=', $tag );
});
