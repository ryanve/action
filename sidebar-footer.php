<?php
namespace theme;

if ( is_active_sidebar('footer') ) { ?>

        <div class="widget-area footer-widget-area">
            <ul><?php dynamic_sidebar('footer'); ?></ul>
        </div>

<?php } ?>