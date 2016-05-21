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
            $this->load->library('doctrine');
            $em = $this->doctrine->em;
            $data['records'] = $em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id')), array('id' => 'DESC'), 5, 0);
            $data['filesCount'] = count($em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id'))));
            $data['user_email'] = $this->session->userdata('email');
            $this->load->view('profile/show_profile_view', $data);
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    public function updatePassword() {
        if ($this->session->userdata('logged') == 'true') {
            $this->form_validation->set_rules('inputCurrentPassword', 'Current password', 'required');
            $this->form_validation->set_rules('inputNewPassword', 'New password', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->load->library('doctrine');
                $em = $this->doctrine->em;
                $data['records'] = $em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id')), array('id' => 'DESC'), 5, 0);
                $data['filesCount'] = count($em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id'))));
                $data['user_email'] = $this->session->userdata('email');
                $this->load->view('profile/show_profile_view', $data);
            } else {
                $this->load->library('doctrine');
                $em = $this->doctrine->em;
                $user = $em->getRepository('Entity\User')->findOneBy(array('id' => $this->session->userdata('user_id')));
                $this->validateUpdatePassword($this->input->post('inputCurrentPassword'), $this->input->post('inputNewPassword'), $user);
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    private function validateUpdatePassword($inputCurrentPassword, $inputNewPassword, $user) {
        if($user->getHash() == md5($inputCurrentPassword)) {
            $user->setHash(md5($inputNewPassword));
            $this->load->library('doctrine');
                $em = $this->doctrine->em;
            $em->persist($user);
            $em->flush();
            $data['records'] = $em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id')), array('id' => 'DESC'), 5, 0);
            $data['filesCount'] = count($em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id'))));
            $data['user_email'] = $this->session->userdata('email');
            $data['success'] = "User password was updated";
            $this->load->view('profile/show_profile_view', $data);
        } else {
            $data['records'] = $em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id')), array('id' => 'DESC'), 5, 0);
            $data['filesCount'] = count($em->getRepository('Entity\Record')->findBy(array('user_id' => $this->session->userdata('user_id'))));
            $data['user_email'] = $this->session->userdata('email');
            $data['password_error'] = "Incorrect current password";
            $this->load->view('profile/show_profile_view', $data);
        }
    }

}
