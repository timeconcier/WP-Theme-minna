<?php
/*
 * パーツ：事業者 管理画面ヘッダ
 */
$themeUrl = get_stylesheet_directory_uri();
$uid      = get_current_user_id();
$epName   = get_user_meta($uid, '事業所名');
$page 	  = get_post( get_the_ID() );
$slug 	  = (!empty($page)) ? $page->post_name : '';
if(is_user_logged_in() && $slug === 'login') {
	if( current_user_can('administrator') or current_user_can('editor') ) {
		header('Location: '.home_url('/member'));
		exit;
	} else {
		wp_logout();
	}
} elseif(!is_user_logged_in() && $slug !== 'login') {
	$redirect_to = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: '.home_url('/login?redirect_to='.$redirect_to));
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php if (is_singular() && pings_open(get_queried_object())) : ?>
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>

	<?php
		$parent_id 	 =  $post->post_parent or null;
		$parent_slug =  $post->post_name or null;
		if($parent_slug != 'member') {
			$parent_name = get_post($parent_id)->post_title or '';
		}
	?>
	<title><?= $parent_name ? $parent_name.' - ' : ''; ?><?= $post->post_title; ?> | <?= bloginfo('name'); ?></title>

	<script>
		const uid = <?= $uid ?>;
	</script>
	<style>
		[v-cloak] {
			display: none;
		}
	</style>
</head>

<body <?php body_class(); ?>>
	<v-app
		id="app"
		v-cloak
		:style="{ background: $vuetify.theme.themes.light.background }"
	>
		<?php if(is_user_logged_in()): ?>
			<template>
				<vuetify-navbar user-name="<?= $epName[0] ?>"/>
			</template>
		<?php endif; ?>

		<v-main>