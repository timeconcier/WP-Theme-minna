<?php
get_header();
get_template_part('searchform');
$themeUrl = get_stylesheet_directory_uri();
?>


<div class="bg-dark text-center py-2 mb-3">
  <h3 class="m-0 text-white">新着情報</h3>
</div>

<div class="container">
  <div class="row">

    <?php
    $postTypes = [
      'enterprises' => [
        'name' => '事業紹介',
        'color' => 'success',
      ],
      // 'job_offers'  => [
      //     'name' => '求人',
      //     'color' => 'info',
      // ],
      // 'events'      => [
      //     'name' => 'イベント',
      //     'color' => 'warning',
      // ],
      // 'coupons'     => [
      //     'name' => 'クーポン',
      //     'color' => 'danger',
      // ],
    ];
    $args = array(
      'posts_per_page' => 12,
    );
    foreach ($postTypes as $pt => $ptn) {
      $args['post_type'][] = $pt;
    }

    foreach (get_posts($args) as $post) : setup_postdata($post);
      $post_id = $post->ID;

      $thumbnail = null;
      if ($post->post_type == 'events') {
        if (get_field('ギャラリー')) $thumbnail = get_field('ギャラリー')[0]['url'];
      } else {
        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium');
      }
      $thumbnail = (has_post_thumbnail($post_id)) ? $thumbnail : wp_get_attachment_url(NO_IMAGE_ID);
    ?>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?php the_permalink() ?>" class="col-6 col-sm-4 p-2 mb-2">
          <div style="height:250px;background-image: url('<?= $thumbnail; ?>');background-size: cover;" class="hover-shadow">
            <div class="caption w-100 d-md-block d-none"><?php the_title() ?></div>
            <div class="caption w-100 d-block d-md-none" style="bottom:0;"><?php the_title() ?></div>
            <span class="badge bg-<?= $postTypes[$post->post_type]['color']; ?> rounded-pill m-1"><?= $postTypes[$post->post_type]['name']; ?></span>
          </div>
        </a>
      </div>
    <?php endforeach;
    wp_reset_postdata();
    wp_reset_query(); ?>

  </div>

  <!-- <div class="mb-3 px-2 d-flex justify-content-end">
        <a href="" class="btn btn-primary h4 d-flex gap-2 align-items-center">
            <span>もっと見る</span>
            <i class="fas fa-chevron-right"></i>
        </a>
    </div> -->
</div>



<?php get_footer(); ?>