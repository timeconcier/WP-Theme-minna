<?php
  $dsp_name   = get_the_author_meta('display_name');
  $thumbnail  = get_the_post_thumbnail_url();

  $color = ($post_type == 'parentings') ? 'success' : (($post_type == 'recipes') ? 'info' : 'primary');
?>


<div id="scroll-show-nav" class="w-100 position-fixed top-0 start-0 d-none shadow-sm" style="z-index:9999;">
  <div class="position-absolute w-100 h-100 overflow-hidden bg-<?= $color; ?>" style="mix-blend-mode:multiply;"></div>

  <nav class="navbar navbar-light bg-white d-flex justify-content-between bg-transparent align-items-center py-2 px-3" style="min-height:55px;">

    <h2 class="h3 fw-bold text-white d-block m-0" style="max-width:calc(100% - 55px);"><?= $dsp_name; ?></h2>

  </nav>
</div>

<article class="w-100">


  <div class="position-relative">
    <div class="d-flex align-items-center bg-<?= $color; ?> px-3 py-2 w-100 shadow-sm" style="width:calc(100% - 20px);top:10px;">

      <h2 class="text-white m-0 h4 ps-3"><?= get_the_title(); ?></h2>
      <div class="flex-grow-1"></div>

    </div>

  </div>

  <div class="bg-secondary w-100 mt-2">
    <div class="w-100 d-sm-flex mx-auto mb-2 justify-content-center align-items-end gap-2" style="max-width:800px;">
      <?php if (has_post_thumbnail()) : ?>
        <img src="<?= $thumbnail; ?>" alt="<?= get_the_title(); ?>" class="d-block align-self-start mx-auto" style="max-width:50%">
      <?php endif; ?>
      <div class="d-flex flex-column align-self-stretch mx-auto mt-2" style="min-width:300px; max-width:50%;">
        <div class="d-flex flex-column p-2 card mt-auto">
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

  <div class="py-4 w-100 mx-auto" style="max-width:800px;">
    <?= get_the_content(); ?>
  </div>

  <?php // タグ一覧
  $tags = get_the_terms($post->ID, "cat_{$post_type}");
  if ($tags) :
    foreach ($tags as $tag) :
  ?>
    <small class="badge rounded-pill fw-normal bg-warning m-1 px-3 py-1"><?= $tag->name; ?></small>
  <?php endforeach; endif; ?>
</article>
