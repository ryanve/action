<?php 
namespace theme;
?>

    <?php do_action( '@before_footer' ); ?>
    
    <footer id="footer">

        <?php do_action( '@footer' ); ?>

    </footer>
    
    <?php do_action( '@after_footer' ); ?>

    <?php wp_footer(); ?>

</body>
</html>