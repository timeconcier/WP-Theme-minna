<?php
/*
パーツ：サイトヘッダ
*/
$themeUrl = get_stylesheet_directory_uri();
$postType = getPostType();
// print_r(wp_get_current_user()->roles);
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
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />

	<?php if(is_home() or is_front_page()): ?>
		<title><?php bloginfo('name'); ?> | <?php bloginfo('description') ?></title>
	<?php else: ?>
		<title><?php bloginfo('name'); ?><?= wp_title(); ?></title>
	<?php endif; ?>


	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-212452885-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-212452885-1');
		const postType = '<?= $postType; ?>';
		const permalink = '<?= get_permalink(); ?>';
	</script>
</head>
<!-- <a href='<?= home_url(); ?>/linelogin/' class='line-login-link login'>LINE Login</a> -->

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<?php if(get_post_status() == 'draft' or is_page() and $_GET['id'] and $_GET['preview']): ?>
		<div class="bg-danger text-white text-center fw-bold p-2">プレビュー表示中</div>
	<?php endif; ?>

	<header>
		<div class="d-flex py-3 justify-content-center d-none d-sm-block">
			<?php if (is_front_page() && is_home()) : ?>
				<h1 class="sitelogo h3 my-2 text-center"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
			<?php else : ?>
				<p class="sitelogo h3 my-2 text-center"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></p>
			<?php endif; ?>
		</div>
		<nav class="navbar navbar-expand-lg py-2 navbar-dark bg-dark d-none d-sm-block">
			<?php get_template_part('template-parts/navbar', null, array('post_type' => $postType)); ?>
		</nav>
		<nav class="navbar navbar-expand-lg py-2 navbar-dark bg-dark d-sm-none d-block position-fixed top-0 w-100" style="z-index:999;">
			<?php get_template_part('template-parts/navbar', null, array('post_type' => $postType)); ?>
		</nav>
	</header>
	<!-- /サイトヘッダ -->

	<div class="d-sm-none d-block" style="margin-top:55px;"></div>

	<?php
		$null = null;

		$pickups = get_posts(array(
			'post_type'		 => 'pickups',
			'numberposts' => -1,
		));
		if (is_front_page() and count($pickups) > 0) :
	?>

		<!-- Slider main container -->
		<div class="w-100" style="background-color: #ccc;">
			<div id="front-swiper" class="swiper mx-auto" style="max-width: 1000px;">
				<!-- Additional required wrapper -->
				<div class="swiper-wrapper">
					<!-- Slides -->
					<?php foreach($pickups as $pu): setup_postdata( $pu ); ?>
					<div class="swiper-slide" style="background-image: url('<?= get_the_post_thumbnail_url(get_field('事業所', $pu->ID)); ?>');background-size: cover;">
						<div data-swiper-parallax="-50" class="swiper-overlay d-flex">
							<div class="w-100 d-flex flex-column">
								<div class="d-sm-block d-none">
									<span class="badge rounded-pill bg-success h5">Pickup!</span><br>
								</div>
								<span class="fw-bold mt-auto text-white" style="color:#555;">
									<?= str_replace("\n", "<br>", get_field('紹介文', $pu->ID)); ?>
								</span>
								<div class="d-flex align-items-end mt-auto">
									<div class="d-block d-sm-none justify-self-start">
										<span class="badge rounded-pill bg-success h5 m-0">Pickup!</span><br>
									</div>

									<div class="flex-grow-1"></div>

									<a href="<?= get_permalink(get_field('事業所', $pu->ID)); ?>" class="btn btn-sm btn-primary d-flex align-items-center gap-2" style="min-width:100px">
										<span>詳しく見る</span>
										<i class="fas fa-chevron-right"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>


	<?php if(is_search() || is_archive()): ?>
		<div class="position-relative py-4" style="background-image: url(https://minna.digital-town.jp/wp-content/themes/minna_no_site-v2/_images/bg_town.png); background-repeat:repeat-x;background-position:bottom;">
			<div class="w-100 mx-auto px-2" style="max-width:1200px;">
				<?php get_template_part('searchform'); ?>
			</div>
		</div>
	<?php elseif (is_home() || is_front_page()): ?>
		<?php if (!strstr(get_bloginfo('url'), 'minna')) : ?>
			<div class="position-relative py-4" style="background-image: url(https://minna.digital-town.jp/wp-content/themes/minna_no_site-v2/_images/bg_town.png); background-repeat:repeat-x;background-position:bottom;">
				<div class="w-100 mx-auto px-2" style="max-width:1200px;">
					<?php get_template_part('searchform'); ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="mx-auto pt-2" style="max-width:1200px;">