<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged')) {
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
            redirect('main/home');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('main/login_form');
            } else if ($this->isValidUser($this->input->post('email'), $this->input->post('password'))) {
                $this->session->set_userdata('logged', 'true');
                $this->session->set_userdata('email', $this->input->post('email'));
                $this->load->library('doctrine');
                $em = $this->doctrine->em;
                $data['records'] = $em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id')));
                $data['user_email'] = $this->session->userdata('email');
                $this->load->view('main/home_view', $data);
            } else {
                $data['login_error'] = "Invalid data";
                $this->load->view('main/login_form', $data);
            }
        }
    }

    private function isValidUser($user_email, $user_password) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $user = $em->getRepository('Entity\User')->findOneBy(array('email' => $user_email));
        if($user != null) {
            if($user->getHash() == md5($user_password)) {
                $this->session->set_userdata('user_id', $user->getId());
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function logout() {
        if ($this->session->userdata('logged') == 'true') {
            $this->session->set_userdata('logged', 'false');
            $this->load->view('main/login_form');
        } else {
            $this->load->view('main/login_form');
        }
    }

    public function home() {
        if ($this->session->userdata('logged') == 'true') {
            $this->load->library('doctrine');
            $em = $this->doctrine->em;
            $data['records'] = $em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id')));
            $data['user_email'] = $this->session->userdata('email');
            $this->load->view('main/home_view', $data);
        } else {
            $this->load->view('main/login_form');
        }
    }

    public function showSearch() {
        if ($this->session->userdata('logged') == 'true') {
            $this->load->library('doctrine');
            $em = $this->doctrine->em;
            $data['records'] = $em->getRepository("Entity\Record")->createQueryBuilder('o')
                    ->where('o.name LIKE :searchparam AND o.user_id = :user_id')
                    ->setParameter('searchparam', '%' . $this->input->post('search') . '%')
                    ->setParameter('user_id', $this->session->userdata('user_id'))
                    ->getQuery()
                    ->getResult();
            $data['searchParam'] = $this->input->post('search');
            $data['user_email'] = $this->session->userdata('email');
            $this->load->view('main/search_view', $data);
        } else {
            $this->load->view('main/login_form');
        }
    }

}
