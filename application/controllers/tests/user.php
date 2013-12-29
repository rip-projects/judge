<?php

/**
 * user.php
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

class user extends Unit_Controller {

    function test_one() {
        $this->unit->run(10*10, 100, 'will be ok');
    }

    function test_two() {
        $this->unit->run(10*10, 90, 'will be error');
    }

    function test_three() {
        $test = 1 + 1;

        $expected_result = 2;

        $test_name = 'Adds one plus one';

        $this->unit->run($test, $expected_result, $test_name);
    }

}
