<?php
namespace theme;

\ob_start(function($html) {
    # The outbut buffer usage here is experimental and powerful.
    # All content must go between ob_start and ob_get_flush.
    return apply_filters('@output', $html);
}); 

?><!DOCTYPE html>
<?php 
    # Let the entire tag be hooked so that IE conditions could be added
    # see @html_atts in functions.php (includes language_attributes)
    echo apply_filters('@html_tag', '<html>');
?>

<head>
<?php wp_head(); # Load all head content via this hook. ?>
</head>

<body<?php
    # Re: http://github.com/ryanve/action/commit/ee589a0bc03f5720e3e28404a6118d9934755805
    echo rtrim(' ' . apply_filters('@body_atts', 'class="' . \implode(' ', get_body_class()) . '"'));
?>>

<?php do_action('@body'); # Load all body parts via this hook. ?>

<?php wp_footer(); # Ensure that this is last. ?>

</body>
</html><?php \ob_get_flush();
#end