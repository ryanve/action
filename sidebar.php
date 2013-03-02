<?php
namespace theme;

#  @link  codex.wordpress.org/Function_Reference/register_sidebars
#  @link  codex.wordpress.org/Function_Reference/register_sidebar
?>

<?php if ( is_active_sidebar('sidebar') ) { ?>

    <aside id="sidebar" class="widget-area sidebar">
        <ul><?php dynamic_sidebar(); ?></ul>
    </aside>

<?php } ?>
