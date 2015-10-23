<?php

/**
 * barcode.php
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

class Barcode extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function generate($barcodes, $texts) {
        $barcodes = $this->uri->rsegments[3];
        $texts = $this->uri->rsegments[4];

        $data['barcodes'] = explode(',', $barcodes);
        $data['texts'] = explode(',', $texts);
        $this->load->view('barcode/generate', $data);
    }

    function generate_image() {

        $barcode = $this->uri->rsegments[3];
        $text = $this->uri->rsegments[4];
        $data['barcode'] = $barcode;
        $data['text'] = $text;
        $data['width'] = 350;
        $this->load->view('barcode/barcode', $data);
    }

}
