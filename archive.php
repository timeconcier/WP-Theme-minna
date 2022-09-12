<?php
get_header(); ?>

<div class="mx-auto" style="max-width:100%;width:800px;">

<?php
    get_template_part('template-parts/list_template', null, array(
        'post_type' => get_query_var( 'post_type' ),
        'params'    => $_GET,
    ));
?>

</div>

<?php get_footer(); ?>