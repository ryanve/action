<?php
namespace theme;

#  @link  codex.wordpress.org/Function_Reference/register_sidebars
#  @link  codex.wordpress.org/Function_Reference/register_sidebar
?>

<?php if ( is_active_sidebar('sidebar') ) { ?>

    <aside class="widget-area sidebar-widget-area">
        <ul><?php dynamic_sidebar('sidebar'); ?></ul>
    </aside>

<?php } ?>
