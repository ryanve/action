<?php
namespace theme;

# Markup to display at the top of .hfeed in loop.php
# codex.wordpress.org/images/1/18/Template_Hierarchy.png
#
# I put this in a separate file so that child themes can more easily  override the loop 
# in components. I wanted to handle this all via hooks but some of the needed WP funcs 
# only echo and it would be messy. This seems to be the cleanest way for now. If we were 
# to break these into separate files that might give betters options for loading them in 
# custom ways, such as to grab an hcard for a user while on another page, or to load a 
# header for a custom loop. Submit ideas @link github.com/ryanve/action/issues
?>

        <?php if ( is_tag() || is_category() || is_tax() ) { ?>

            <header class="loop-header tax-header">
                <h1 class="loop-title tax-title"><a href=""><?php single_term_title(); ?></a></h1>
                <div class="loop-desc tax-desc"><?php echo term_description( '', get_query_var( 'taxonomy' ) ); ?></div>
            </header>

        <?php } elseif ( is_author() ) { ?>

            <header class="loop-header user-header vcard"><!-- microformats.org/wiki/hcard -->
                <h1 class="loop-title user-title fn n"><?php the_author_meta( 'display_name' ); ?></h1>
                <div class="profile-pic"><?php echo apply_filters( '@profile-pic', null ); ?></div>
                <div class="loop-desc user-desc"><?php the_author_meta( 'description' ); ?></div>
            </header>

        <?php } elseif ( is_date() ) { ?>

            <header class="loop-header date-header">
                <h1 class="loop-title date-title"><?php \call_user_func(function() {
                    $parts = \explode( '/', \ltrim($_SERVER['REQUEST_URI'], '/') );
                    $title = array();
                    while ( \is_numeric($n = \array_shift($parts)) )
                        $title[] = $n;
                    echo \implode( '-', $title );
                }); ?></h1>
                <div class="loop-desc date-desc"><?php _e('Archives.', 'theme'); ?></div>
            </header>

        <?php } elseif ( is_search() ) { ?>

            <header class="loop-header search-header">
                <h1 class="loop-title search-title"><?php echo ( __('Search: ', 'theme') . get_search_query() ); ?></h1>
            </header>

        <?php } elseif ( is_post_type_archive() ) { ?>
        
            <header class="loop-header">
                <h1 class="loop-title"><?php post_type_archive_title(); ?></h1>
                <div class="loop-desc"><?php echo ( get_post_type_object( get_query_var('post_type') )->description ); ?></div>
            </header>
            
        <?php } elseif ( ! is_singular() ) { ?>

            <!--
            <header class="loop-header assistive">
                <h1 class="loop-title"><?php _e('Posts', 'theme'); ?></h1>
            </header>
            -->

        <?php } ?>