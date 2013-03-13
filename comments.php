<?php
namespace theme;

# adapted from @link bit.ly/github-twentytwelve
# try to be as simple and semantic as possible

if ( 'comments.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) )
	exit;

if ( post_password_required() || ! post_type_supports( get_post_type(), 'comments' ) )
	return;
?>

    <!--
        @link microformats.org/wiki/hcomment
        @link microformats.org/wiki/comments-formats
        @link microformats.org/wiki/comment-brainstorming
        @link microformats.org/wiki/xoxo
    -->

<?php 
    # There is currently no official microformat for comments. 
    # hcomment is used in comment.php for symmetry with hentry in loop.php
    # Should .comments be on the container or the list? .hfeed? See links above.
    # Todo: maybe add filter(s) for the attrs.
?>

                        <aside <?php echo ((is_singular() ? 'id="comments"' : '') . (' class="comments"')); ?>>

                            <?php if ( have_comments() ) { ?>
                            
                                <h2 class="loop-title comments-title"><?php 
                                    comments_number(); 
                                ?></h2>

                                <ol class="xoxo comments"><?php
                                    # see the '@list_comments' filter in functions.php
                                    wp_list_comments( apply_filters( '@list_comments', array() ) ); 
                                ?></ol><!-- /.xoxo -->

                                <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // "paged" comments ?>
                                <nav>
                                    <h3 class="assistive"><?php _e('Comment navigation', 'theme'); ?></h3>
                                    <?php previous_comments_link( apply_filters( '@comments_older', __('&laquo; Older', 'theme') ) ); ?>
                                    <?php next_comments_link( apply_filters( '@comments_newer', __('Newer &raquo;', 'theme') ) ); ?>
                                </nav>
                                <?php } ?>

                            <?php } ?>
                            
                            <?php 
                                if ( comments_open() )
                                    comment_form();
                                else echo '<p class="status">' . __('Comments are closed.', 'theme') . '</p>';
                            ?>

                        </aside>