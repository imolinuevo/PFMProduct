<?php

class Cipher {

    protected $cipher;
    protected $key;
    protected $data;
    private $mode;
    private $initializationVector;

    public function __construct($text, $key, $initializationVector) {
        $this->cipher = MCRYPT_RIJNDAEL_256;
        $this->key = $key;
        $this->data = $text;
        $this->mode = MCRYPT_MODE_CFB;
        ($initializationVector == null) ? $this->initializationVector = mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND) : $this->initializationVector = $initializationVector;
    }

    public function getInitializationVector() {
        return $this->initializationVector;
    }

    public function isValid() {
        return ($this->data != null && $this->key != null && $this->cipher != null ) ? true : false;
    }

    public function encrypt() {
        if ($this->isValid()) {
            return trim(base64_encode(mcrypt_encrypt($this->cipher, $this->key, $this->data, $this->mode, $this->initializationVector)));
        } else {
            throw new Exception('[Invalid Options]');
        }
    }

    public function decrypt() {
        if ($this->isValid()) {
            return trim(mcrypt_decrypt($this->cipher, $this->key, base64_decode($this->data), $this->mode, $this->initializationVector));
        } else {
            throw new Exception('[Invalid Options]');
        }
    }

}
