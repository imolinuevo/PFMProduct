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
            $this->load->view('record/new_record_view', $data);
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    public function showRecord() {
        if ($this->session->userdata('logged') == 'true') {
            if($this->recordExists()) {
                $data['user_email'] = $this->config->item('user_email');
                $this->load->view('record/show_record_view', $data);
            } else {
                $data['user_email'] = $this->config->item('user_email');
                $this->load->view('main/home_view', $data);
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    private function recordExists() {
        //TODO
        return true;
    }
}