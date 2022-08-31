<?php
/**
 * Template Name: プレビュー
 */
get_header();

if (isset($_GET['pt']) and isset($_GET['id'])) {
  $wp_query = new WP_Query(array(
    'post__in'    => array($_GET['id']),
    'post_type'   => $_GET['pt'],
    'post_status' => 'any',
  ));
  if ($_GET['id'] and $_GET['preview'] and $wp_query -> have_posts()) {
    while ($wp_query -> have_posts()) {
      the_post();
      get_template_part("template-parts/{$_GET['pt']}-template");
    }
  }
}

get_footer();