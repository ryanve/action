<?php
namespace theme;

# See required hooks:
# codex.wordpress.org/Theme_Review#Template_Tags_and_Hooks

?><!DOCTYPE html>
<?php echo apply_filters( '@html_tag', '<html>' ); ?>

<head>
<?php wp_head(); # use action hooks ?>
</head>

<?php echo apply_filters( '@body_tag', '<body class="'. \implode( ' ', get_body_class() ) . '">' ); ?>

    <?php do_action( '@before_header' ); ?>
    
    <header id="header" role="banner">

        <?php do_action( '@header' ); ?>

    </header><!-- #header -->
    
    <?php do_action( '@after_header' );
