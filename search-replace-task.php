<?php
/*
Plugin Name: Search Replace Task
Description: Challenge task
Version: 1.0
Author: Alan Benedict Golpeo
*/

require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

function fetch_remote($slug, $search_for, $replace_with) {
    $url = get_option('REMOTE_URL');
    $type = $_POST['content_type'];
    $response = wp_remote_get("$url/wp-json/wp/v2/$type/?slug=$slug");
    if (is_array($response) && !is_wp_error($response)) {
        $posts = json_decode(wp_remote_retrieve_body($response), true);

        if(count($posts) > 0) {
            $page_id = $posts[0]['id'];


            $content = $posts[0]['content']['rendered'];
            $excerpt = $posts[0]['excerpt']['rendered'];

            if ((strpos($content, $search_for) !== false) || strpos($excerpt, $search_for) !== false) {
                $content = str_replace($search_for, $replace_with, $content);
                $excerpt = str_replace($search_for, $replace_with, $content);

                execute_update($page_id, $content, $excerpt);
            } else {
                echo '  
                    <div class="notice notice-error is-dismissible">
                        <p>No string matches.</p>
                    </div>';
            }
        }
        else {
            display_error(true);
        }
    } else {
        display_error();
    }
}

function execute_update($page_id, $content, $excerpt) {
    try {
        $client = new Client();
    
        $url = get_option('REMOTE_URL');
        $response = $client->request('PUT', "$url/wp-json/wp/v2/pages/$page_id", [
            'json' => [
                'content' => $content,
                'excerpt' => $excerpt
            ],
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(get_option('REMOTE_USERNAME') .':'. get_option('REMOTE_APP_PASSWORD'))
            ]
        ]);
    
        $body = $response->getBody();
    
        display_success();
    }
    catch(Exception) {
        display_error();
    }
}

function display_error($not_auth = null) {
    echo '
        <div class="notice notice-error is-dismissible">
            <p>
                Failed to apply change. ';

    if (!$not_auth) {
        echo 'Check Remote Credentials or url';
    }
    echo '</p>
        </div>';
}
function display_success() {
    echo '
        <div class="notice notice-success is-dismissible">
            <p>Action successful!</p>
        </div>';
}



// ==================================================================================================

function search_replace_page_content() {
    $page_name = 'Search Replace Task';

    if (isset($_POST['custom_button'])) {

        if($_POST['slug'] == '' || $_POST['search_for'] == '' || $_POST['replace_with'] == '') {
            echo '
            <div class="notice notice-error is-dismissible">
                <p>Please fill all fields!</p>
            </div>';
        }
        else {
            fetch_remote($_POST['slug'], $_POST['search_for'], $_POST['replace_with']);
        }
    }

    ?>
    
    <div class='wrap'>
        <h2> <?php echo $page_name; ?> </h2>
        <p>Welcome to the custom settings page.</p>

        <div style='display: flex; align-items: flex-start; justify-content: center; flex-direction: column;'>
            <form method='post' action='' style='display: flex; align-items: flex-end; flex-direction: column;'>
                <div style='margin: 10px;'>
                    <label for='content_type'>
                        Content type
                    </label>
                    <select name="content_type" id="custom_content_type" style='width: 193px;'>
                        <option value="pages" selected>Page</option>
                        <option value="posts">Post</option>
                    </select>
                </div>
                <div style='margin: 10px;'>
                    <label for='slug'>
                        Slug
                    </label>
                    <input name='slug' type='text'/>
                </div>
                <div style='margin: 10px;'>
                    <label for='search_for'>
                        Search for
                    </label>
                    <input name='search_for' type='text'/>
                </div>
                <div style='margin: 10px;'>
                    <label for='replace_with'>
                        Replace with
                    </label>
                    <input name='replace_with' type='text'/>
                </div>
                <input name='custom_button' class='button button-primary' type='submit' value='Replace'/>
            </form>
        </div>
    </div>

    <?php
}

function custom_settings_page() {
    $page_name = 'Search Replace Task';
    add_menu_page($page_name, $page_name, 'manage_options', 'search-replace-settings', 'search_replace_page_content');

}


add_action('admin_menu', 'custom_settings_page');

include 'authentication.php';