<?php
namespace theme;

global $comment;
?>

    <!--
        There is currently no official microformat for comments. 
        hcomment is used for symmetry with hentry
        @link microformats.org/wiki/hcomment
        @link microformats.org/wiki/comments-formats
    -->

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

	</li>
    