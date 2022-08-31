<?php

/**
 *パーツ：サイトフッター
 */
$themeUrl  = get_stylesheet_directory_uri();
?>
<!-- サイドバー -->
<!-- <div id="sidebar" class="col-12 col-md-3">
	<div class="login-button d-md-block d-none mb-3">
		<a href="https://member.digital-town.jp/" class="btn btn-warning w-100" target="_blank">事業者ログイン</a>
	</div>

    <?php //get_template_part("template-parts/search_filter"); ?>

	</div> -->
</div>

<!-- サイトフッタ -->
<footer class="bg-primary py-3 px-2">
	<div style="max-width: 1000px;" class="mx-auto subpages d-flex gap-2 flex-wrap justify-content-center mb-3">
		<a href="https://anniversaryconcier.jp/" class="text-white">運営者について</a>
		<span>/</span>
		<a href="https://digital-town.jp/#contact" class="text-white ">お問い合わせ</a>
	</div>

	<div class="text-center py-2 mb-3">
		<h2 class="text-white h4">みんなのまちサイト</h2>
	</div>

	<div class="text-center py-2 mb-3">
		<small class="text-white">
			©<?= date('Y'); ?> デジタル町一丁目
			<span class="d-inline-block">powered by</span>
			<span class="d-inline-block">アニバーサリーコンシェル株式会社</span>
		</small>
	</div>
</footer>
<!-- /サイトフッタ -->

<?php wp_footer(); ?>
<?php if (is_singular()) wp_enqueue_script("comment-reply"); ?>
</body>

</html>