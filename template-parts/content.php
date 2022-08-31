<?php
/**
* パーツ：記事一覧のループ部分
*/
?>

	<!-- 記事ヘッダの出力 -->
	<?php get_template_part( 'template-parts/contentheader' ); ?>

	<!-- entry-content -->
	<section class="entry-container">
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</section>
	<!-- /entry-content -->

	<!-- entry-footer -->
	<div class="entry-footer">
		<!-- コメントの表示 -->
		<?php comments_template(); ?>
	</div>
	<!-- /entry-footer -->
