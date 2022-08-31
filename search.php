<?php
get_header();

// $post_type = get_post_type
$query = $wp_query->query;
if ($query['post_type']) {
  $post_type = $query['post_type'];
} elseif ($_GET['pt']) {
  $post_type = $_GET['pt'];
} else {
  $post_type = 'enterprses';
}
get_template_part("template-parts/list-template", null, array(
  'post_type' => $post_type,
  'params'    => $_GET,
));

get_footer();