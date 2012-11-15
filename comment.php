<?php
namespace theme;

global $comment;
?>

	<li id="comment-<?php comment_ID(); ?>">

		<?php do_action( '@before_comment' ); ?>

		<article class="hcomment">

			<?php do_action( '@open_comment' ); ?>

            <?php echo apply_filters( '@comment_avatar', get_avatar( $comment, 90 ) ); ?>

			<?php echo apply_filters( '@comment_meta', '<div class="comment-meta">[comment-author] [comment-published] [comment-permalink before="| "] [comment-edit-link before="| "] [comment-reply-link before="| "]</div>' ); ?>

			<div class="comment-content"><?php
                if ( ! $comment->comment_approved )
                    echo apply_filters( '@comment_moderation', '<p class="alert moderation">' . __( 'Your comment is awaiting moderation.' ) . '</p>' );

                comment_text( $comment->comment_ID );
            ?></div><!-- /.comment-content -->

			<?php do_action( '@close_comment' ); ?>

		</article><!-- /.hcomment -->

		<?php do_action( '@after_comment' ); ?>

    <?php 
    # "Note the lack of a trailing </li>. WordPress will add it 
    # itself once it's done listing any children and whatnot."
    # codex.wordpress.org/Function_Reference/wp_list_comments

    