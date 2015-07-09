<?php

defined('ABSPATH') or exit;

class YandexMail
{

    const DOMAIN = 'yandex-mail';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
        if (isset($_POST['action']) && $_POST['action'] == 'test') {
            add_action('init', [$this, 'do_test']);
        }
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_init', [$this, 'admin_init']);
        if (!extension_loaded('openssl')) {
            add_action('admin_notices', [$this, 'ssl_required_notice']);
        }
    }

    public function init()
    {
        load_plugin_textdomain(self::DOMAIN, false, basename(__DIR__) . '/languages');
        $this->options = get_option('yandex_mail');
    }

    public function do_test()
    {
        check_admin_referer('test-options');
        $result = wp_mail($_POST['to'], $_POST['subject'], $_POST['message']);
        add_action('admin_notices', [$this, ($result ? 'test_ok_notice' : 'test_error_notice')]);
    }

    public function admin_menu()
    {
        $title = __('Yandex Mail', self::DOMAIN); 
        add_options_page($title, $title, 'manage_options', 'yandex-mail', [$this, 'admin_page']);
    }

    public function admin_init()
    {
        register_setting('yandex_mail', 'yandex_mail');
        add_settings_field('from', __('From Name', self::DOMAIN), [$this, 'field_callback'],
            'yandex-mail', 'default', ['type' => 'text', 'name' => 'from']);
        add_settings_field('login', __('Login', self::DOMAIN), [$this, 'field_callback'],
            'yandex-mail', 'default', ['type' => 'mail', 'name' => 'login']);
        add_settings_field('password', __('Password', self::DOMAIN), [$this, 'field_callback'],
            'yandex-mail', 'default', ['type' => 'password', 'name' => 'password']);
        add_settings_field('copy', __('Send yourself a copy', self::DOMAIN), [$this, 'copy_callback'],
            'yandex-mail', 'default');
    }

    public function admin_page()
    {
        echo '<div class="wrap">'
            . '<h2>' . __('Yandex Mail', self::DOMAIN) . '</h2>'
            . '<form method="post" action="options.php">';
        settings_fields('yandex_mail');
        echo '<table class="form-table">';
        do_settings_fields('yandex-mail', 'default');
        echo '</table>';
        submit_button(); 
        echo '</form>'
            . '<h3>' . __('Test Email', self::DOMAIN) . '</h3>'
            . '<form method="post">'
            . '<input type="hidden" name="action" value="test" />';
        wp_nonce_field('test-options');
        echo '<table class="form-table">'
            . '<tr><th scope="row">' . __('To', self::DOMAIN) . '</th>'
            . '<td><input type="email" name="to" /></td></tr>'
            . '<tr><th scope="row">' . __('Subject', self::DOMAIN) . '</th>'
            . '<td><input type="text" name="subject" /></td></tr>'
            . '<tr><th scope="row">' . __('Message', self::DOMAIN) . '</th>'
            . '<td><textarea name="message" style="width: 100%; height: 5rem;"></textarea></td>'
            . '</tr></table>'
            . '<p class="submit">'
            . '<input type="submit" class="button" value="' . __('Send Test Mail', self::DOMAIN) . '">'
            . '</p></form></div>';
    }

    public function field_callback($args)
    {
        echo '<input type="' . $args['type'] . '" name="yandex_mail[' . $args['name'] . ']" value="'
            . esc_attr($this->options[$args['name']]) . '" required="required" autocomplete="false" />';
    }

    public function copy_callback()
    {
        echo '<input type="checkbox" name="yandex_mail[copy]" value="1"'
            . checked($this->options['copy'], 1, false) . ' />';
    }
    
    public function ssl_required_notice()
    {
        echo '<div class="error"><p>'
            . __('The mod_ssl module is required', self::DOMAIN)
            . '</p></div>';
    }

    public function test_ok_notice()
    {
        echo '<div class="updated"><p>'
            . __('A test email has been successfully sent', self::DOMAIN)
            . '</p></div>';
    }

    public function test_error_notice()
    {
        echo '<div class="error"><p>'
            . __('A test email has not been sent', self::DOMAIN)
            . '</p></div>';
    }

}

new YandexMail;
