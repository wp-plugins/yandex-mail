<?php
/*
Plugin Name: Yandex Mail
Text Domain: yandex-mail
Plugin URI: http://gzep.ru/yandex-mail-wordpress-plugin/
Description: This plugin gives you the easiest way to send emails through the Yandex SMTP server instead of PHP mail().
Version: 1.0
Author: Gaiaz Iusipov
Author URI: http://gzep.ru
License: MIT
*/

defined('ABSPATH') or exit;

if (is_admin()) {
    require __DIR__ . '/options.php';
}

add_action('phpmailer_init', function($phpmailer) {
    require __DIR__ . '/init.php';
});
