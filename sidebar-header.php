<?php
namespace theme;

if ( is_active_sidebar('header') ) { ?>

        <aside class="widget-area header-widget-area">
            <ul><?php dynamic_sidebar( 'header' ); ?></ul>
        </aside>

<?php } ?>