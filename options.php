<?php

defined('ABSPATH') or exit;

class YandexMail
{

    const DOMAIN = 'yandex-mail';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
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

    public function admin_menu()
    {
        $title = __('Yandex Mail', self::DOMAIN); 
        add_options_page($title, $title, 'manage_options', 'yandex-mail', [$this, 'admin_page']);
    }

    public function admin_init()
    {
        register_setting('yandex_mail', 'yandex_mail');
        add_settings_section('default', false, false, 'yandex-mail');
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
        do_settings_sections('yandex-mail');
        submit_button(); 
        echo '</form></div>';
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

}

new YandexMail;
