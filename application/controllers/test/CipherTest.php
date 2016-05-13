<?php

require_once('../Cipher.php');

class CipherTest extends PHPUnit_Framework_TestCase
{
    private $cipher;
    
    public function testConstructor() {
        $this->cipher = new Cipher("content", "key", null);
        $this->assertNotNull($this->cipher->getInitializationVector());
    }
    
    public function testIsValid() {
        $this->cipher = new Cipher("content", "key", null);
        $this->assertTrue($this->cipher->isValid());
    }
    
    public function testEncrypt() {
        $this->cipher = new Cipher("content", str_pad("key", 32, STR_PAD_RIGHT), null);
        $this->assertNotEquals("content", $this->cipher->encrypt());
    }
    
}