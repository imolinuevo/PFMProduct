<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged')) {
            $this->session->set_userdata('logged', 'false');
        }
    }

    public function index() {
        if ($this->session->userdata('logged') == 'true') {
            redirect('/main/home');
        } else {
            redirect('/main/login');
        }
    }

    public function login() {
        if ($this->session->userdata('logged') == 'true') {
            $this->load->view('home_view');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('login_form');
            } else {
                $this->session->set_userdata('logged', 'true');
                $this->load->view('home_view');
            }
        }
    }
    
    public function logout() {
        if ($this->session->userdata('logged') == 'true') {
            $this->session->set_userdata('logged', 'false');
            $this->load->view('login_form');
        } else { 
            $this->load->view('login_form');
        }
    }
    
    public function home() {
        if ($this->session->userdata('logged') == 'true') {
            $this->load->view('home_view');
        } else {
            $this->load->view('login_form');
        }
    }

}
