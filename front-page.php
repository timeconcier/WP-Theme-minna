<?php
get_header();
// get_template_part('searchform');
$themeUrl = get_stylesheet_directory_uri();
?>

<div class="bg-dark text-center py-2 mb-3">
  <h3 class="m-0 text-white">新着情報</h3>
</div>
<div class="container">
  <div class="row">

  <?php
    if (strstr(get_bloginfo('url'), 'minna')):
      $postTypes = [
        'enterprises' => [
          'name' => '事業紹介',
          'color' => 'success',
        ],
      ];
      $args = array( 'posts_per_page' => 12 );
      foreach ($postTypes as $pt => $ptn) $args['post_type'][] = $pt;

      foreach (get_posts($args) as $post) :
        setup_postdata($post);
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
  <?php endforeach; wp_reset_postdata(); wp_reset_query(); ?>

  <?php else: ?>
        <?php
          $i = 0;
          $post_types = [];
          foreach(get_post_types( array( 'public'  => true, '_builtin' => false ) ) as $pt => $name) $post_types[] = $name;

          $args   = array(
            'posts_per_page' => 20,
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'paged'          => $paged,
            'tax_query'      => array(
              'relation' => 'AND',
            ),
            'order'        => 'DESC',
          );
          $the_query = new WP_Query($args);

          if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();
            $i++;
            $post_id    = get_the_ID();
            $thumnbnal  = get_the_post_thumbnail_url($post_id);
            $permaUrl   = get_permalink();

            $tags = [];
            if (is_array($post_types)) {
              foreach($post_types as $pt) {
                if (taxonomy_exists("cat_{$pt}")) {
                  $_tags = get_the_terms($post->ID, "cat_{$pt}");
                  foreach($_tags as $_tag) $tags[] = $_tag;
                }
              }
            } else {
              $tags = get_the_terms($post->ID, "cat_{$post_types}");
            }
          ?>

            <div class="col-12 col-sm-6 mb-3">
              <div class="d-flex justify-content-between align-items-center bg-primary p-2 gap-1">
                <a href="<?= $permaUrl; ?>" class="fw-bold text-white"><?= the_title(); ?></a>
              </div>
              <div class="d-sm-flex bg-white mb-3 w-100 border border-primary border-top-0">
                <?php if($thumnbnal): ?>
                <div class="mx-auto" style="width:100%;max-height:180px;">
                  <a href="<?= $permaUrl; ?>">
                    <img src="<?= $thumnbnal; ?>" class="d-block w-100" style="height:180px;object-fit:cover;" alt="">
                  </a>
                </div>
                <?php endif; ?>

                <div class="d-flex flex-column justify-content-between gap-1 p-2" style="<?= ($thumnbnal) ? 'min-width:calc(100% - 200px);' : 'width:100%;' ?>">
                  <?php
                    $content = get_field('記事情報');
                    $content = wp_strip_all_tags( $content );
                    $content = strip_shortcodes( $content );
                    if (mb_strlen($content)>50) {
                      $content = wp_trim_words($content, 50, '…' );
                    }
                  ?>
                  <span><?= $content ?></span>
                  <!-- タグ一覧 -->
                  <?php if($tags and count($tags) > 0) : ?>
                    <div class="d-flex flex-wrap">
                      <?php foreach($tags as $tag): ?>
                        <span class="badge rounded-pill fw-normal bg-warning m-1 px-3 py-1" style="font-size:9pt;"><?= $tag->name; ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>

                  <!-- 投稿者 -->
                  <div>
                    <small class="fw-bold d-flex align-items-center gap-2">
                      <span style="width:5rem;"><i class="fas fa-edit">投稿者</i></span>
                      <?= the_author_meta('display_name'); ?>
                    </small>
                    <small class="fw-bold d-flex align-items-center gap-2">
                      <span style="width:5rem;"><i class="fas fa-clock">投稿日時</i></span>
                      <?= get_the_date('Y年m月d日  H:i'); ?>
                    </small>
                  </div>
                </div>
              </div>
            </div>

          <?php endwhile; else : ?>

          <div class="col card w-100 align-items-center px-2 mx-2 mb-4" style="height:100px;">
            <p class="my-auto fw-bold">お探しの<?= $post_type_name; ?>は見つかりませんでした。</p>
          </div>

        <?php endif; wp_reset_postdata(); ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>