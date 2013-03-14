<?php
namespace theme;

# Markup to display at the top of loop.php
# codex.wordpress.org/images/1/18/Template_Hierarchy.png

?>

            <header class="loop-header"><?php
                do_action( '@loop_header' ); 
            ?></header>

