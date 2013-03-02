<?php
namespace theme;

#  @link  codex.wordpress.org/Function_Reference/register_sidebars
#  @link  codex.wordpress.org/Function_Reference/register_sidebar
?>

<?php if ( is_active_sidebar('footer') ) { ?>

        <div class="footer-widget-area">
            <ul><?php dynamic_sidebar( 'footer' ); ?></ul>
        </div>

<?php } ?>