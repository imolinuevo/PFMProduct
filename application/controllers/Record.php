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

    public function createRecord() {
        if ($this->session->userdata('logged') == 'true') {
            $this->form_validation->set_rules('fileName', 'File name', 'required');
            $this->form_validation->set_rules('pinCode', 'Pin code', 'required|min_length[4]|max_length[4]');
            if (empty($_FILES['inputFile']['name'])) {
                $this->form_validation->set_rules('inputFile', 'Input file', 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $data['user_email'] = $this->config->item('user_email');
                $this->load->view('record/new_record_view', $data);
            } else {
                if($this->fileNameIsUnique($this->input->post('fileName'))) {
                    $this->load->library('doctrine');
                    $content = file_get_contents($_FILES['inputFile']['tmp_name']);
                    $key = str_pad($this->input->post('pinCode'), 32, STR_PAD_RIGHT);
                    require_once('Cipher.php');
                    $myCipher = new Cipher($content, $key, null);
                    $result = $myCipher->encrypt();
                    $vector = trim(base64_encode($myCipher->getInitializationVector()));
                    $record = new Entity\Record($this->input->post('fileName'), $_FILES['inputFile']['type'], $_FILES['inputFile']['size'], new DateTime(), md5(trim(base64_encode($content))), $vector, $result);
                    $this->persistRecord($record);
                    $data['user_email'] = $this->config->item('user_email');
                    $this->load->view('main/home_view', $data);
                } else {
                    $data['create_error'] = "There is already a file with that name.";
                    $data['user_email'] = $this->config->item('user_email');
                    $this->load->view('record/new_record_view', $data);
                }
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    private function fileNameIsUnique($name) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $record = $em->getRepository('Entity\Record')->findOneBy(array('name' => $name));
        if($record == null) {
            return true;
        } else {
            return false;
        }
    }

    private function persistRecord($record) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $em->persist($record);
        $em->flush();
    }

    public function showRecord($recordId) {
        if ($this->session->userdata('logged') == 'true') {
            if (isset($recordId) && $this->recordExists($recordId)) {
                $this->load->library('doctrine');
                $em = $this->doctrine->em;
                $data['record'] = $em->getRepository('Entity\Record')->find($recordId);
                $data['user_email'] = $this->config->item('user_email');
                $this->load->view('record/show_record_view', $data);
            } else {
                redirect('/main/home');
            }
        } else {
            $this->load->view('main/login_form');
        }
    }

    private function recordExists($recordId) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $record = $em->getRepository('Entity\Record')->find($recordId);
        if($recordId != NULL && $record != NULL) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteRecord($recordId) {
        if ($this->session->userdata('logged') == 'true') {
            if (isset($recordId) && $this->recordExists($recordId)) {
                $this->load->library('doctrine');
                $em = $this->doctrine->em;
                $record = $em->getRepository('Entity\Record')->findOneBy(array('id' => $recordId));
                $em->remove($record);
                $em->flush();
                redirect('/main/home');
            } else {
                redirect('/main/home');
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
}
