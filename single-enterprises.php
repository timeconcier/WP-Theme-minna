<?php
get_header();
while (have_posts()) : the_post();
  $pTemplate = get_field('ページテンプレート');
  $epId      = get_the_author_meta('ID');
  $epName    = get_the_author_meta('display_name');
  $epTel     = get_the_author_meta('電話番号');

  $pageColor = colorType($post->ID);
?>

<article class="w-100">
  <?php get_template_part("template-parts/enterprises-template-{$pTemplate}"); ?>

  <div class="d-flex">
    <div class="flex-grow-1"></div>
    <small>
      <span>削除依頼等についてはこちら：</span>
      <a href="tel:088-832-1221">088-832-1221</a>
    </small>
  </div>
</article>


<?php endwhile; ?>
<?php get_footer();
