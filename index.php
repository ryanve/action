<?php
namespace theme;

# the outbut buffer usage here is experimental and powerful
# the content must go between ob_start and ob_get_flush
# see functions.php
\ob_start(function ( $html ) {
    return apply_filters( '@output', $html );
});

get_header(); ?>

    <?php do_action( '@before_main' ); ?>
    
    <div id="main" role="main">
    
        <?php do_action( '@main' ); ?>

    </div><!-- #main -->
    
    <?php do_action( '@after_main' ); ?>
        
<?php
get_sidebar(); 

get_footer();

\ob_get_flush(); 

#end