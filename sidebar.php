<?php
namespace theme; 

if ( is_active_sidebar('main') ) { ?>

        <aside class="widget-area main-widget-area">
            <ul><?php dynamic_sidebar('main'); ?></ul>
        </aside>

<?php } ?>