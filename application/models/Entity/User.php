<?php

namespace Entity;

/**
 *
 *
 * @Entity
 * @Table(name="user")
 * @author  Iñigo Molinuevo
 */
class User {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $email;
    
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $hash;
    
    public function __construct($email, $hash) {
        $this->email = $email;
        $this->hash = $hash;
    }
    
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function setHash($hash) {
        $this->hash = $hash;
        return $this;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getHash() {
        return $this->hash;
    }
}