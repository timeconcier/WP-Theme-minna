<?php

/**
 *パーツ：サイトフッター
 */
$themeUrl = get_stylesheet_directory_uri();
?>
		</v-main>
		<template>
			<div class="text-center">
				<v-overlay :value="loading">
				<v-progress-circular
					indeterminate
					size="64"
				></v-progress-circular>
				</v-overlay>
			</div>
		</template>



		<v-footer app>
			<v-spacer></v-spacer>
			<span>©みんなのまちサイト<span class="d-sm-inline d-none"> powered by <a href="https://anniversaryconcier.jp/">アニバーサリーコンシェル Corp.</a></span></span>
			<v-spacer></v-spacer>
		</v-footer>
	</v-app>
<!-- /サイトフッタ -->

<?php wp_footer(); ?>
<?php if (is_singular()) wp_enqueue_script("comment-reply"); ?>
</body>

</html>