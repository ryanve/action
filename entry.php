<?php 
namespace theme;
# See '@entry' in hooks.php
?>

            <article <?php echo apply_filters('@entry_atts', ''); ?>>

                <?php do_action('@entry'); # all entry parts load via this hook ?>

            </article>

