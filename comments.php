<?php
/**
 *パーツ：コメント
 */
if ( post_password_required() ) {
	return;
}
?>

<aside class="comment-container">

	<?php if ( have_comments() ) : ?>
		<h3>コメント</h3>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
				) );
			?>
		</ol>

	<div class="commentpagelink">
	<?php paginate_comments_links(); ?>
	</div>
	<?php endif; ?>

	<?php comment_form(); ?>

</aside>
