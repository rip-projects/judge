<?php

/**
 * service.php
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
 * Description of service
 *
 * @author jafar
 */
class Service extends Cli_Controller {

	// example of translate chat service
    function translate() {
        $this->load->library('svc_system', NULL, 'svc');
        $this->svc->run();
        exit;
    }

}