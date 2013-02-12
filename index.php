<?php
namespace theme;
# see functions.php

\ob_start(function ( $html ) {
    # the outbut buffer usage here is experimental and powerful
    # the content must go between ob_start and ob_get_flush
    return apply_filters( '@output', $html );
}); 

?><!DOCTYPE html>
<?php echo apply_filters( '@html_tag', '<html>' ); # @html_attrs in functions.php ?>

<head>
<?php wp_head(); # all head tags load via this hook ?>
</head>

<?php echo apply_filters( '@body_tag', '<body>' ); # @body_attrs in functions.php ?>

<?php do_action( '@body' ); # all sections load via this hook ?>

<?php wp_footer();  # ensure that this is last ?>

</body>
</html><?php \ob_get_flush(); 

#end