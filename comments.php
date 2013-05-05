<?php
namespace theme;
# adapted from @link bit.ly/github-twentytwelve
# Try to be as simple and semantic as possible.

if ('comments.php' === \basename($_SERVER['SCRIPT_FILENAME']))
	exit;

if (post_password_required() || ! post_type_supports(get_post_type(), 'comments'))
	return;
?>

<?php 
    # github.com/ryanve/action/issues/1
    # There is currently no official microformat for comments. 
    # hcomment is used in comment.php for symmetry with hentry.
    # microformats.org/wiki/hcomment
    # microformats.org/wiki/xoxo
?>

                <aside <?php echo ((is_singular() ? 'id="comments"' : '') . (' class="comments"')); ?>>

                    <?php if (have_comments()) { ?>
                    
                        <h2 class="loop-title comments-title"><?php 
                            comments_number(); 
                        ?></h2>

                        <ol class="xoxo comments clearfix"><?php
                            # see the '@list_comments' filter in functions.php
                            wp_list_comments(apply_filters('@list_comments', array())); 
                        ?></ol>

                        <?php if (1 < get_comment_pages_count() && get_option('page_comments')) { ?>
                        <nav>
                            <h3 class="assistive"><?php _e('Comment navigation', 'theme'); ?></h3>
                            <?php previous_comments_link(apply_filters('@comments_older', __('&laquo; Older', 'theme'))); ?>
                            <?php next_comments_link(apply_filters('@comments_newer', __('Newer &raquo;', 'theme'))); ?>
                        </nav>
                        <?php } ?>

                    <?php } ?>
                    
                    <?php 
                        if (comments_open())
                            comment_form(apply_filters('@comment_form', array()));
                        else echo '<p class="status">' . __('Comments are closed.', 'theme') . '</p>';
                    ?>

                </aside>
