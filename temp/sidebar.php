<?php
namespace theme;

#  @link  codex.wordpress.org/Function_Reference/register_sidebars
#  @link  codex.wordpress.org/Function_Reference/register_sidebar
?>

<?php if ( is_active_sidebar('sidebar') ) { ?>

    <aside class="sectioning widget-area sidebar-widget-area">
        <ul class="grouping"><?php dynamic_sidebar('sidebar'); ?></ul>
    </aside>

<?php } ?>
