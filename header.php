<?php
namespace theme;

# See required hooks:
# codex.wordpress.org/Theme_Review#Template_Tags_and_Hooks

?>

    <?php do_action( '@before_header' ); ?>

    <header id="header" role="banner">

        <?php do_action( '@header' ); ?>

    </header>
    
    <?php do_action( '@after_header' );
