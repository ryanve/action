<?php
namespace theme;

#  @link  codex.wordpress.org/Function_Reference/register_sidebars
#  @link  codex.wordpress.org/Function_Reference/register_sidebar
?>

<?php if ( is_active_sidebar('header') ) { ?>

        <div class="widget-area header-widget-area">
            <ul><?php dynamic_sidebar( 'header' ); ?></ul>
        </div>

<?php } ?>
