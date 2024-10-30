<?php
/*
 * Plugin Name: Captcha Novami
 * Description: Captcha Novami works like an invisible reCaptcha. No settings required. Just install and activate.
 * Version: 1.1
 * Author: Michal Novák
 * Author URI: https://www.novami.cz
 * License: GPL3
 * Text Domain: captcha-novami
 */

/**
 * Class CaptchaNovami
 */
class CaptchaNovami
{
    const TEXT_DOMAIN = 'captcha-novami';

    const TOKEN = 'canoto';

    /** @var string */
    private $cookieToken;

    /**
     * CaptchaNovami constructor.
     */
    public function __construct()
    {
        if (!defined('ABSPATH')) {
            die('Direct access not allowed!');
        }

        $this->getToken();
        $this->check();

        add_action('init', [$this, 'loadJs']);
    }

    public function loadJs()
    {
        $scriptName = sprintf('%s_js', str_replace('-', '_', self::TEXT_DOMAIN));
        wp_register_script($scriptName, sprintf('%s/main.js', plugin_dir_url(__FILE__)), ['jquery']);
        wp_enqueue_script($scriptName);
        wp_localize_script($scriptName, sprintf('%s_trans', $scriptName), [
            'loaded' => __('Captcha Novami is active!', self::TEXT_DOMAIN),
            'canoto' => $this->cookieToken
        ]);
    }

    /**
     * @param int $length
     * @return string
     */
    private function getRandomToken(int $length)
    {
        $token = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        while (strlen($token) < $length) {
            $token .= $chars[rand(0, strlen($chars) - 1)];
        }

        return ($token);
    }

    private function getToken()
    {
        $this->cookieToken = isset($_COOKIE[self::TOKEN]) ? esc_attr($_COOKIE[self::TOKEN]) : null;

        if (!$this->cookieToken) {
            $token = $this->getRandomToken(20);
            $this->cookieToken = $token;
            setcookie(self::TOKEN, $token, 0, '/', null, 1, 1);
        }
    }

    /**
     * @return bool
     */
    private function activateProtection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !wp_doing_ajax() && !self::is_rest_request() && !isset($_GET['_locale']) && !isset($_POST['customize_theme'])) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private static function is_rest_request()
    {
        return defined(REST_REQUEST) ? REST_REQUEST : false;
    }

    /**
     * @return bool
     */
    private function check()
    {
        $postToken = isset($_POST[self::TOKEN]) ? esc_attr($_POST[self::TOKEN]) : null;

        if ($this->activateProtection() && ($this->cookieToken !== $postToken || !$postToken)) {
            $title = __('Forbidden', self::TEXT_DOMAIN);
            $error = __('ERROR:', self::TEXT_DOMAIN);
            $reason = __('Captcha verification failed.', self::TEXT_DOMAIN);
            $homepage = sprintf('<a href="%s">%s</a>', get_home_url(), __('&laquo; Homepage', self::TEXT_DOMAIN));
            $message = sprintf('<strong>%s</strong> %s<br/><p>%s</p>', $error, $reason, $homepage);

            wp_die($message, $title, ['response' => 403]);
        }

        return true;
    }
}

new CaptchaNovami();
