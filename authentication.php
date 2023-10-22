<?php

function my_text_plugin_submenu() {
    add_submenu_page('search-replace-settings', 'Submenu Page 1', 'Remote Authentication', 'manage_options', 'my-text-plugin-submenu-1', 'my_text_plugin_submenu_page_1');
}

function my_text_plugin_submenu_page_1() {

    ?>

        <div class="wrap">
            <form method="post" action="options.php">
                <?php settings_fields('my_text_plugin_options'); ?>
                <?php do_settings_sections('my-text-plugin'); ?>
                <input type="submit" class="button button-primary" value="Save">
            </form>
        </div>
    <?php

}



function init_remote_credentials() {
    register_setting('my_text_plugin_options', 'REMOTE_URL');
    register_setting('my_text_plugin_options', 'REMOTE_USERNAME');
    register_setting('my_text_plugin_options', 'REMOTE_APP_PASSWORD');

    add_settings_section('text_plugin_section', 'Remote Authentication', 'remote_username_label_callback', 'my-text-plugin');

    add_settings_field('REMOTE_URL', 'Wordpress Site Domain', 'remote_url_callback', 'my-text-plugin', 'text_plugin_section');
    add_settings_field('REMOTE_USERNAME', 'Username', 'remote_username_callback', 'my-text-plugin', 'text_plugin_section');
    add_settings_field('REMOTE_APP_PASSWORD', 'Application Password', 'remote_app_password_callback', 'my-text-plugin', 'text_plugin_section');
}

function remote_username_label_callback() {
    echo 'Enter the username and Application Password to access remote site:';
}

function remote_url_callback() {
    $text = get_option('REMOTE_URL');
    echo '<input type="text" name="REMOTE_URL" value="' . esc_attr($text) . '">';
}

function remote_username_callback() {
    $text = get_option('REMOTE_USERNAME');
    echo '<input type="text" name="REMOTE_USERNAME" value="' . esc_attr($text) . '">';
}

function remote_app_password_callback() {
    $text = get_option('REMOTE_APP_PASSWORD');
    echo '<input type="password" name="REMOTE_APP_PASSWORD" value="' . esc_attr($text) . '">';
}


add_action('admin_init', 'init_remote_credentials');
add_action('admin_menu', 'my_text_plugin_submenu');
