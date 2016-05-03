<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
    
    function __construct() {
        parent::__construct();
    }
    
    public function index() {
        redirect('main');
    }
    
    public function showProfile() {
        if ($this->session->userdata('logged') == 'true') {
            $data['user_email'] = $this->config->item('user_email');
            $this->load->view('profile/show_profile_view', $data);
        } else {
            $this->load->view('main/login_form');
        }
    }

}