<?php
global $_GET;
$site_url       = get_bloginfo('url');

$this_post_type = $args->query['post_type'] ? '/'.$args->query['post_type'] : '';
$this_page_num  = $args->query['paged'];
$max_page_num   = ($args->max_num_pages) ? $args->max_num_pages : 1;

$prev_page = ($this_page_num == 1) ? 'disabled' : '';
$next_page = ($this_page_num == $max_page_num) ? 'disabled' : '';

$prev_page_num  = $this_page_num - 1;
$next_page_num  = $this_page_num + 1;

$q = '';
if(!empty($_GET)) $q = '?'.http_build_query($_GET);
?>

<div class="d-flex flex-wrap justify-content-between align-items-stretch mb-4">
    <div class="prev-buttons d-flex gap-1">
        <a href="<?= $site_url.$this_post_type.'/'.$q; ?>" class="btn btn-dark d-flex gap-2 align-items-center <?= $prev_page; ?>">
            <i class="fas fa-angle-double-left"></i>
            <span class="d-none d-sm-inline">最初へ</span>
        </a>
        <a href="<?= $site_url.$this_post_type.'/page/'.$prev_page_num.$q; ?>" class="btn btn-dark d-flex gap-2 align-items-center <?= $prev_page; ?>">
            <i class="fas fa-angle-left"></i>
            <span class="d-none d-sm-inline">前へ</span>
        </a>
    </div>

    <div class="border border-dark d-flex align-items-center px-2">
        <span><?= $this_page_num; ?><span class="mx-1">/</span><?= $max_page_num; ?></span>
    </div>

    <div class="next-buttons d-flex gap-1">
        <a href="<?= $site_url.$this_post_type.'/page/'.$next_page_num.$q; ?>" class="btn btn-dark d-flex gap-2 align-items-center <?= $next_page; ?>">
            <span class="d-none d-sm-inline">次へ</span>
            <i class="fas fa-angle-right"></i>
        </a>
        <a href="<?= $site_url.$this_post_type.'/page/'.$max_page_num.$q; ?>" class="btn btn-dark d-flex gap-2 align-items-center <?= $next_page; ?>">
            <span class="d-none d-sm-inline">最後へ</span>
            <i class="fas fa-angle-double-right"></i>
        </a>
    </div>
</div>