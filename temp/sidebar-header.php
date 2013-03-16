<?php
namespace theme;

#  @link  codex.wordpress.org/Function_Reference/register_sidebars
#  @link  codex.wordpress.org/Function_Reference/register_sidebar
?>

<?php if ( is_active_sidebar('header') ) { ?>

        <aside class="sectioning widget-area header-widget-area">
            <ul class="grouping"><?php dynamic_sidebar('header'); ?></ul>
        </aside>

<?php } ?>
