<?php
namespace theme;

global $comment;
?>

	<li id="comment-<?php comment_ID(); ?>">

		<?php do_action( '@before_comment' ); ?>

		<article class="comment">

			<header class="comment-header">
                <?php echo apply_filters( '@comment_avatar', get_avatar( $comment, 60 ) ); ?>
                <!-- '@comment_meta' -->
                <dl class="meta-list">
                    <dt><?php _e('By'); ?></dt><dd><?php echo get_comment_author_link(); ?></dd>
                    <dt><?php _e('Published'); ?></dt><dd><?php echo get_comment_date(); ?></dd>
                </dl>
            </header>

			<div class="comment-content"><?php
                if ( ! $comment->comment_approved )
                    echo apply_filters( '@comment_moderation', '<p class="alert moderation">' . __( 'Your comment is awaiting moderation.' ) . '</p>' );

                comment_text( $comment->comment_ID );
            ?></div>

		</article>

		<?php do_action( '@after_comment' ); ?>

    <?php 
    # "Note the lack of a trailing </li>. WordPress will add it 
    # itself once it's done listing any children and whatnot."
    # codex.wordpress.org/Function_Reference/wp_list_comments

    