<?php
ini_set("display_errors", "Off");
header('Content-Type: application/json; charset=utf-8');
// add_action( 'rest_api_init', function () {
// 	$post_types = json_decode(file_get_contents(__DIR__.'/custom_post_types.json'));
//     foreach($post_types as $i => $pt) {
//         register_rest_route( $pt->name, "{$pt->path}", array(
//             'methods' => 'GET',
//             'callback' => 'get_custom_posts_api',
//         ) );
//     }

//     register_rest_route( 'api', 'users', array(
//         'methods' => 'GET',
//         'callback' => 'get_users_api',
//     ) );
// });

function get_custom_posts_api( $data ) {
    $route       = $data->get_route();
    $route_paths = array_merge(array_filter(explode('/', $route)));
    $endpoint    = $route_paths[0];
    $path        = $route_paths[1];

    $params = $_GET ? $_GET : array();
    $args   = array(
        'post_type' => $endpoint,
        'posts_per_page' => -1,
    );

    $args   = array_merge($args, $params);
    $query  = get_posts( $args );

    $posts = array();
    if( count($query) > 0 ) {
        foreach ( $query as $post ) {
            $post = json_decode(json_encode($post), true);
            $post_meta = get_post_meta( $post['ID'] );
            $meta_data = array();
            foreach($post_meta as $key => $meta) {
                if(substr($key, 0, 1) !== '_') $meta_data[$key] = $meta[0];
            }

            $posts[] = array_merge($post, $meta_data);
        }
    }

    echo json_encode( $posts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}



function get_users_api($data) {
    $users  = array();
    $args   = $_GET ? $_GET : null;
    $get_users = get_users( $args );

    foreach ( $get_users as $user ) {
        $user = json_decode(json_encode($user), true);
        unset($user['data']);
        unset($user['allcaps']);
        $user_meta = get_user_meta( $user['ID'] );
        $meta_data = array();
        foreach($user_meta as $key => $meta) {
            if(substr($key, 0, 1) !== '_') $meta_data[$key] = $meta[0];
        }

        $users[] = array_merge($user, $meta_data);
    }

    echo json_encode( $users, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}