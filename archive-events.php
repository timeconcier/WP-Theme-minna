<?php
/** ********************************************************************************
 *  参画企業一覧
 ******************************************************************************** */
get_header();

$template_part = 'template-parts/list-template';
get_template_part($template_part, null, array(
  'post_type' => 'events',
  'params'    => $_GET,
));

get_footer();