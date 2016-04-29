<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Record extends CI_Controller {
    
    function __construct() {
        parent::__construct();
    }
    
    public function index() {
        redirect('main');
    }
    
    public function showCreateRecord() {
        if ($this->session->userdata('logged') == 'true') {
            $data['user_email'] = $this->config->item('user_email');
            $this->load->view('new_record_view', $data);
        } else {
            $this->load->view('login_form');
        }
    }
}