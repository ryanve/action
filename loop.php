<?php 
namespace theme;
?>

        <!-- 
            @link microformats.org/wiki/hatom
            @link microformats.org/wiki/hcard
            @link microformats.org/wiki/hentry
            @link stackoverflow.com/a/7295013/770127
        -->
        <div class="loop hfeed" itemscope itemtype="http://schema.org/Blog">
            
            <?php do_action( '@loop' ); ?>

        </div><!-- .loop -->
        
