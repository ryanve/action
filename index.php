<?php
namespace theme;

# the outbut buffer usage here is experimental and powerful
# the content must go between ob_start and ob_get_flush
# see functions.php
\ob_start(function ( $html ) {
    return apply_filters( '@output', $html );
}); 

?><!DOCTYPE html>
<?php echo apply_filters( '@html_tag', '<html>' ); ?>

<head>
<?php wp_head(); # use action hooks ?>
</head>

<?php echo apply_filters( '@body_tag', '<body class="'. \implode( ' ', get_body_class() ) . '">' ); ?>

<?php get_header(); ?>

    <?php do_action( '@before_main' ); ?>
    
    <div id="main" role="main">
    
        <?php do_action( '@main' ); ?>

    </div><!-- #main -->
    
    <?php do_action( '@after_main' ); ?>
        
<?php get_sidebar(); ?>

<?php get_footer(); ?>

<?php wp_footer(); ?>

</body>
</html><?php \ob_get_flush(); 

#end