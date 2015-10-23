<?php

/**
 * xldap.php
 *
 * @package     arch-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

$config = array(
        'hostname' => 'semut-srv.xinix-technology.com',
        'port' => 389,
        'protocol_version' => 3,
        'base_dn' => 'dc=xinix,dc=co,dc=id',
        'user_dn' => 'cn=admin,dc=xinix,dc=co,dc=id',
        'password' => 'password',
        'user_base' => 'ou=employees,dc=xinix,dc=co,dc=id',
        'query' => '(cn=%s)',
        'fields' => array ('cn','mail'),
);