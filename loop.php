<?php 
namespace theme;
?>

        <!-- 
            @link microformats.org/wiki/hatom
            @link microformats.org/wiki/hcard
            @link microformats.org/wiki/hentry
            @link stackoverflow.com/a/7295013/770127
        -->
        <section class="hfeed">
            
            <?php do_action( '@loop' ); ?>

        </section><!-- .hfeed -->
        
