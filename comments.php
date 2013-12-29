<?php
namespace theme;
# Adapted from http://bit.ly/github-twentytwelve
# Try to be as simple and semantic as possible.
# http://github.com/ryanve/action/issues/1
# There is currently no official microformat for comments. 
# hcomment is used in comment.php for symmetry with hentry.
# http://microformats.org/wiki/hcomment
# http://microformats.org/wiki/xoxo

if ('comments.php' === \basename($_SERVER['SCRIPT_FILENAME'])) exit;
if (post_password_required() || ! post_type_supports(get_post_type(), 'comments')) return;

call_user_func(function($hook, $tagname, $handler = 'do_action') {
    echo "<$tagname" . \rtrim(' ' . apply_filters($hook . '_atts', '')) . '>';
    $handler($hook);
    echo "</$tagname>\n\n";
}, '@comments', 'aside', function() {
    if (have_comments()) {
        echo '<h2 class="loop-title comments-title">';
        comments_number();
        echo '</h2><ol class="xoxo comments clearfix">';
        wp_list_comments(apply_filters('@list_comments', array())); 
        echo '</ol>';
        if (1 < get_comment_pages_count() && get_option('page_comments')) {
            echo '<nav><h3 class="assistive">' . __('Comment navigation', 'theme') . '</h3>';
            previous_comments_link(apply_filters('@comments_older', __('&laquo; Older', 'theme')));
            next_comments_link(apply_filters('@comments_newer', __('Newer &raquo;', 'theme')));
            echo '</nav>';
        }
    }
    if (comments_open()) comment_form(apply_filters('@comment_form', array()));
    else echo '<p class="status">' . __('Comments are closed.', 'theme') . '</p>';
});