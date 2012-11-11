<?php
namespace theme;

?><!DOCTYPE html>
<?php echo apply_filters('@html_tag', '<html>'); ?>

<head>
<?php wp_head(); ?>
</head>

<?php echo apply_filters('@body_tag', '<body>'); ?>

    <?php do_action( '@before_header' ); ?>
    
    <header data-1.5 id="header" role="banner">

        <?php do_action( '@header' ); ?>

    </header><!-- /#header -->
    
    <?php do_action( '@after_header' );
