<?php
namespace theme;
if ('comments.php' === \basename($_SERVER['SCRIPT_FILENAME'])) exit;

call_user_func(function($hook, $tagname, $handler = 'do_action') {
    echo "<$tagname" . \rtrim(' ' . apply_filters($hook . '_atts', '')) . '>';
    $handler($hook);
    echo "</$tagname>\n\n";
}, '@comments', 'aside');