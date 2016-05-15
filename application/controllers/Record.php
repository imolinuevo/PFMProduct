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
                $this->validateCreateRecord();
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    private function validateCreateRecord() {
        if($this->fileNameIsUnique($this->input->post('fileName'))) {
            $this->load->library('doctrine');
            $content = file_get_contents($_FILES['inputFile']['tmp_name']);
            $key = str_pad($this->input->post('pinCode'), 32, STR_PAD_RIGHT);
            require_once('Cipher.php');
            $myCipher = new Cipher($content, $key, null);
            $result = $myCipher->encrypt();
            $vector = trim(base64_encode($myCipher->getInitializationVector()));
            $record = new Entity\Record($this->input->post('fileName'), pathinfo($_FILES['inputFile']['name'], PATHINFO_EXTENSION), $_FILES['inputFile']['size'], new DateTime(), md5(base64_encode(trim($content))), $vector);
            $this->persistRecord($record, $result);
            redirect('main/home');
        } else {
            $data['create_error'] = "There is already a file with that name.";
            $data['user_email'] = $this->config->item('user_email');
            $this->load->view('record/new_record_view', $data);
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

    private function persistRecord($record, $result) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $em->persist($record);
        $em->flush();
        $file_path = realpath(FCPATH)."/data/".$record->getName();
        $handle = fopen($file_path, 'w') or die("can't open file");
        fwrite($handle, $result);
        fclose($handle);
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
                $this->deleteFile($record->getName());
                redirect('/main/home');
            } else {
                redirect('/main/home');
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    private function deleteFile($name) {
        if(file_exists(realpath(FCPATH)."/data/".$name)) {
            unlink(realpath(FCPATH)."/data/".$name);
        }
    }
    
    public function downloadRecord($recordId) {
        if ($this->session->userdata('logged') == 'true') {
            $this->form_validation->set_rules('pin_code', 'Pin code', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->showRecord($recordId);
            } else {
                $this->validateDownloadRecord($recordId, $this->input->post('pin_code'));
            }
        } else {
            $this->load->view('main/login_form');
        }
    }
    
    private function validateDownloadRecord($recordId, $pin_code) {
        if (isset($recordId) && $this->recordExists($recordId)) {
            $this->load->library('doctrine');
            $em = $this->doctrine->em;
            $record = $em->getRepository('Entity\Record')->find($recordId);
            if($this->isCorrectPinCode($record, $pin_code)) {
                $this->transferRecord($record, $pin_code);
            } else {
                $data['pin_error'] = "Incorrect pin code.";
                $this->load->library('doctrine');
                $em = $this->doctrine->em;
                $data['record'] = $em->getRepository('Entity\Record')->find($recordId);
                $data['user_email'] = $this->config->item('user_email');
                $this->load->view('record/show_record_view', $data);
            }
        } else {
            redirect('/main/home');
        }
    }
    
    private function transferRecord($record, $pin_code) {
        $content = $this->parseResult($record, $pin_code);
        $file_name = $record->getName().".".$record->getExtension();
        
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: ".$record->getSize().";");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Type: application/octet-stream; "); 
        header("Content-Transfer-Encoding: binary");

        echo $content;
    }
    
    private function parseResult($record, $pin_code) {
        $content = file_get_contents(realpath(FCPATH)."/data/".$record->getName());
        $key = str_pad($pin_code, 32, STR_PAD_RIGHT);
        require_once('Cipher.php');
        $myCipher = new Cipher($content, $key, base64_decode($record->getVector()));
        $result = $myCipher->decrypt();
        return $result;
    }
    
    private function isCorrectPinCode($record, $pin_code) {
        $hash = md5(base64_encode(trim($this->parseResult($record, $pin_code))));
        if($hash == $record->getHash()) {
            return true;
        } else {
            return false;
        }
    }
}
