<?php

/**
 * ARCH_Lang.php
 *
 * @package     arch-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

/**
 * Description of ARCH_Lang
 *
 * @author jafar
 */
class ARCH_Lang extends CI_Lang {
    function __construct() {
        $this->_defaults = array(
            'id' => 'id',
            'en' => 'us',
        );

        parent::__construct();
    }

    function load_gettext($language = null, $domain = 'messages') {
        require_once ARCHPATH . '/helpers/x_helper' . EXT;

        @include APPPATH . 'config/' . ENVIRONMENT . '/config' . EXT;
        $tmp = $config;
        $inc = @include(ARCHPATH . 'config/app' . EXT);
        if ($inc) {
            $config = array_merge($tmp, $config);
            $tmp = $config;
        }
        $inc = @include(APPPATH . 'config/' . ENVIRONMENT . '/app' . EXT);
        if ($inc) {
            $config = array_merge($tmp, $config);
        }

        if (!empty($config['language'])) {
            $config_lang = explode('_', $config['language']);
            $default_lang = strtolower($config_lang[0]) . '_' . strtoupper($config_lang[1]) . '.' . $config['charset'];
        }

        $languages = array();
        if (isset($config['lang_force']) && $config['lang_force']) {
            $languages[] = $default_lang;
        } else {
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $accept_lang = explode(',', @$_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach ($accept_lang as &$data) {
                    $t1 = explode(';', $data);
                    $data = $t1[0];
                    $t1 = explode('-', $data);
                    $t1[0] = strtolower($t1[0]);

                    if (count($t1) !== 2) {
                        if (!empty($this->_defaults[$t1[0]])) {
                            $t1[] = $this->_defaults[$t1[0]];
                        }
                    }
                    $data = $t1[0] . ((isset($t1[1])) ? '_' . strtoupper($t1[1]) : '') . '.' . $config['charset'];
                }
                $to_merge = array();
                foreach ($accept_lang as $lang) {
                    $to_merge[] = implode('_', explode('-', $lang));
                }
                $languages = array_merge($languages, $to_merge);
            }
            $languages = array_merge($languages, array($default_lang));
        }

        // get current timestamp
        $current_domain = '';
        foreach ($languages as $language) {
            if (empty($current_domain)) {
                $l = explode('.', $language);
                $domains = glob(APPPATH . 'language/locale/' . strtolower($l[0]) . '/LC_MESSAGES/messages-*.mo');
                if (!empty($domains)) {
                    $current = basename($domains[0], '.mo');
                    $timestamp = preg_replace('{messages-}i', '', $current);
                    $current_domain = $current;
                }
            }
        }

        if (empty($current_domain)) {
            $current_domain = $domain;
        }
        
        // FIXME force first lang only, harusnya semuanya
        $lang = (is_array($languages)) ? $languages[0] : $languages;
        putenv("LC_ALL=" . $lang);
        setlocale(LC_ALL, $lang);
        bindtextdomain($current_domain, APPPATH . 'language/locale');
        textdomain($current_domain);
    }
}

