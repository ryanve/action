<?php
namespace theme;

global $comment;
?>

	<li>
		<article <?php echo apply_filters('@comment_attrs'
         , 'id="comment-' . get_comment_ID() . '" class="comment"'); ?>><?php 
            do_action( '@comment' ); 
        ?></article>

    <?php 
    # "Note the lack of a trailing </li>. WordPress will add it 
    # itself once it's done listing any children and whatnot."
    # codex.wordpress.org/Function_Reference/wp_list_comments

    