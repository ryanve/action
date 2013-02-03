<?php 
namespace theme;

# see '@entry' hooks in functions.php
?>

                    <article <?php echo apply_filters( '@entry_attrs', 
                        'class="' . \implode( ' ', get_post_class() ) . '"' 
                      . ' itemscope itemtype="http://schema.org/Article"' 
                    ); ?>>

                        <?php do_action( '@entry' ); ?>

                    </article>

