<?php
$post_type  = $args['post_type'];
$post_types = array(
  ''            => 'Home',
  'enterprises' => '事業所',
  'job_offers'  => '仕事',
  'coupons'     => 'クーポン',
  'events'      => 'イベント',
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
    }
    ?>
  </span>

  <div style="width:44px"></div>
  <!-- <a class="navbar-toggler text-decoration-none d-md-none d-block py-2" href="#sidebar">
    <i class="fas fa-search"></i>
  </a> -->

  <div class="collapse navbar-collapse justify-content-center" id="navbarColor02">
    <ul class="navbar-nav">
      <?php foreach ($post_types as $pt_slug => $pt_name) : ?>
        <li class="nav-item mx-3">
          <a class="nav-link <?= ($post_type == $pt_slug) ? 'active' : ''; ?>" href="<?= '/' . $pt_slug ?>"><?= $pt_name; ?></a>
        </li>
      <?php endforeach; ?>

      <li class="nav-item mx-3 px-2 d-none d-sm-block bg-warning">
        <a class="nav-link active" href="https://member.digital-town.jp/login" target="_blank">事業者ログイン</a>
      </li>
    </ul>

    <a href="/login" class="btn btn-warning d-block d-sm-none mt-3 w-100">事業者ログイン</a>
  </div>
</div>