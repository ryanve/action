<?php
namespace theme;

\ob_start(function ( $html ) {
    # The outbut buffer usage here is experimental and powerful.
    # All content must go between ob_start and ob_get_flush.
    return apply_filters( '@output', $html );
}); 

?><!DOCTYPE html>
<?php 
    # Let the entire tag be hooked so that IE conditions could be added
    # see @html_attrs in functions.php (defaults incl. language_attributes)
    echo apply_filters( '@html_tag', '<html>' );
?>

<head>
<?php wp_head(); # load all head content via this hook ?>
</head>

<?php 
    # Let the entire tag be hooked for synomity with the html tag.
    # see @body_attrs in functions.php (defaults incl. body_class)
    echo apply_filters( '@body_tag', '<body>' );
?>

<?php do_action( '@body' ); # load all body parts via this hook ?>

<?php wp_footer(); # ensure that this is last ?>

</body>
</html><?php \ob_get_flush();
#end