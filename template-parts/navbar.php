<?php
$post_type  = $args['post_type'];
$post_types = array(
  'minna' => array(
    array('enterprises' => '事業所'),
    array('job_offers'  => '仕事'),
    array('coupons'     => 'クーポン'),
    array('events'      => 'イベント'),
  ),
  'supporter' => array(
    array('parentings'  => '子育て'),
    array('recipes'     => 'レシピ'),
  )
);
?>
<div class="container-fluid">
  <button class="navbar-toggler py-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02" aria-expanded="false" aria-controls="navbarColor02">
    <i class="fas fa-bars"></i>
  </button>

  <span class="d-block d-md-none text-white h5 m-0">
    <?php
    switch ($post_type) {
      case 'enterprises': echo '事業所'; break;
      case 'job_offers':  echo '仕事'; break;
      case 'coupons':     echo 'クーポン'; break;
      case 'events':      echo 'イベント'; break;
      case 'parentings':  echo '子育て'; break;
      case 'recipes':     echo 'レシピ'; break;
    }
    ?>
  </span>

  <div style="width:44px"></div>

  <div class="collapse navbar-collapse justify-content-center" id="navbarColor02">
    <ul class="navbar-nav">
      <a class="nav-link" href="https://minna.digital-town.jp/">Home</a>
      <?php foreach ($post_types as $subdom => $custom_posts) : ?>
        <?php foreach($custom_posts as $custom_post) : ?>
          <?php foreach($custom_post as $post_slug => $post_label) : ?>
            <li class="nav-item mx-3">
              <a class="nav-link <?= ($post_type == $post_slug) ? 'active' : ''; ?>" href="<?= "https://$subdom.digital-town.jp/$post_slug" ?>"><?= $post_label; ?></a>
            </li>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php endforeach; ?>

      <li class="nav-item mx-3 px-2 d-none d-sm-block bg-warning">
        <a class="nav-link active" href="https://member.digital-town.jp/login" target="_blank">事業者ログイン</a>
      </li>
    </ul>

    <a href="/login" class="btn btn-warning d-block d-sm-none mt-3 w-100">事業者ログイン</a>
  </div>
</div>