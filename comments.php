<?php
namespace theme;

# this page is not done yet ( adapted from Hybrid )

if ( 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) )
	die( __( 'This page is not designed to be loaded directly.' ) );

if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) )
	return;
?>

<section id="comments-template">

	<h3 id="comments">One Response to "Hello world!"</h3> 
	<ol class="commentlist">
		<li class="alt" id="comment-1">
			<cite>
	<a href="http://example.org/" rel="nofollow">Mr WordPress</a>
			</cite> Says:<br>
			<small class="commentmetadata">
				<a href="#comment-1" title="">Date and Time</a>
			</small>
				<p>Hi, this is a comment.</p>
		</li>
	</ol>
	<h3 id="respond">Leave a Reply</h3>
	<form action="http://example.com/blog/wp-comments-post.php" method="post" id="commentform">
	<div>
		<input name="author" value="" size="22" tabindex="1" type="text">
			<label for="author">
				<small>Name (required)</small>
			</label>
	</div>
	<div>
		<input name="email" value="" size="22" tabindex="2" type="email">
			<label for="email">
				<small>Mail (will not be published) required)</small>
			</label>
	</div>
	<div>
		<input name="url" value="" size="22" tabindex="3" type="url">
			<label for="url">
				<small>Website</small>
			</label>
	</div>
	<div>
		<small><strong>HTML:</strong> You can use these
		tags:....</small>
	</div>
	<div>
		<textarea name="comment" id="comment" cols="100" rows="10" tabindex="4">
		</textarea>
	</div>
	<div>
		<input name="submit" id="submit" tabindex="5" value="Submit Comment" type="submit">
		<input name="comment_post_ID" value="1" type="hidden">
	</div>
	</form>
	</div>

</section><!-- / -->