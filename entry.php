<?php 
namespace theme;
# see '@entry' in hooks.php
?>

            <article <?php echo apply_filters('@entry_attrs', ''); ?>>

                <?php do_action('@entry'); # all entry parts load via this hook ?>

            </article>

