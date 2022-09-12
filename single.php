<?php
get_header();

while (have_posts()){
  the_post();

  get_template_part("template-parts/single-page-template");
}

get_footer();
