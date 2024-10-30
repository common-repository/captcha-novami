<?php

/**
 * Class CaptchaNovamiUninstall
 */
class CaptchaNovamiUninstall
{
    /**
     * CaptchaNovamiUninstall constructor.
     */
    public function __construct()
    {
        if (!defined('ABSPATH')) {
            exit('Direct access not allowed');
        }
    }

}

new CaptchaNovamiUninstall();
