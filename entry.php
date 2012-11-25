<?php 
namespace theme;

# called within the loop from loop.php
# see '@entry' hooks in functions.php
?>

                    <?php do_action( '@before_entry' ); ?>

                    <article<?php echo \rtrim(' ' . apply_filters( '@entry_attrs', 'class="hentry"' ) ); ?>>

                        <?php do_action( '@entry' ); ?>

                    </article>
                    
                    <?php do_action( '@after_entry' ); ?>

