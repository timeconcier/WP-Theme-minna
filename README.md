# みんなのまちサイト テーマ

## プラグイン
* Advanced Custom Fields  - カスタムフィールド
  * functions/custom_fields.php
* Colorlib 404 Customizer - 404ページ


## カスタム投稿タイプ

以下のファイルで定義しています。
```
functions
├ custom_post_types.json
├ custom_post_types.php
└ custom_taxonomies.php
```

## 関数ファイル
```
functions.php
functions
├ admin_actions.php         - 管理画面用
├ category_actions.php      - タクソノミー関連
├ constants.php             - *定数管理
├ custom_fields.php         - カスタムフィールド定義(ACF用)
├ custom_post_endpoints.php - カスタム投稿用APIエンドポイント(途中)
├ custom_post_types.json    - カスタム投稿タイプ一覧
├ custom_post_types.php     - カスタム投稿タイプ定義
├ custom_taxonomies.php     - カスタムタクソノミー定義
├ front_actions.php         - フロント画面用
├ other.php                 - その他関数
├ post_actions.php          - 投稿関連
├ wp_ajax.php               - 外部からのリクエスト用(マイページ)
└ wp_cron.php               - WP Cron 各種関数
```


## 各テンプレート・ページ詳細

### ページ
```
トップページ        ： front-page.php
各投稿一覧ページ    ： archive-{post_type}.php -> template-parts/list-template.php
検索結果ページ      ： search.php -> template-parts/list-template.php
事業者詳細ページ    ： single-enterprises.php -> template-parts/enterprises-template-a.php
求人詳細ページ      ： single-enterprises.php -> template-parts/job_offer-template.php
イベント詳細ページ  ： single-enterprises.php -> template-parts/events-template.php
クーポン詳細ページ  ： なし
投稿プレビューページ： page-preview.php
```

### パーツ
```
ヘッダーメニュー      ：template-parts/navbar.php
一覧表示              ：template-parts/list-template.php
一覧絞り込み検索      ：template-parts/search-filter.php
一覧ページネーション  ：template-parts/pagination.php
詳細ページシェアボタン：template-parts/share-buttons.php
```

## その他

### Javascript
```
_src/js/action.js
_src/js/script.js
```

### CSS
```
_src/css/bs-complement.css
_src/css/style.css
```