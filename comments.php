<?php
namespace theme;
if ('comments.php' === \basename($_SERVER['SCRIPT_FILENAME'])) exit;
do_action('@' . \basename(__FILE__), 'aside');