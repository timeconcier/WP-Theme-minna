<?php $img_base_url = get_stylesheet_directory_uri().'/_src/icon'; ?>
<div class="w-100 d-flex justify-content-end align-items-center gap-3">
    <span class="share-button" style="cursor:pointer"><i class="bi bi-share-fill"></i></span>
    <a target="_blank" href="https://social-plugins.line.me/lineit/share?url=<?= get_permalink(); ?>"><img class="d-block" style="width:25px;" src="<?= $img_base_url.'/line.svg'; ?>" alt=""></a>
    <a target="_blank" href="https://twitter.com/share?url=<?= get_permalink(); ?>"><img class="d-block" style="width:25px;" src="<?= $img_base_url.'/twitter.svg'; ?>" alt=""></a>
    <a target="_blank" href="http://www.facebook.com/share.php?u=<?= get_permalink(); ?>"><img class="d-block" style="width:25px;" src="<?= $img_base_url.'/facebook.svg'; ?>" alt=""></a>
</div>
