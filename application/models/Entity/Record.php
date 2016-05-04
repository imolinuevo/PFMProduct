<?php

namespace Entity;

/**
 * User Record
 *
 * @Entity
 * @Table(name="record")
 * @author  Iñigo Molinuevo
 */
class Record {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $name;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $extension;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $size;

    /**
     * @Column(type="date", nullable=false)
     */
    protected $date_added;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $hash;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $vector;

    /**
     * @Column(type="text", nullable=false)
     */
    protected $result;

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function setExtension($extension) {
        $this->extension = $extension;
        return $this;
    }
    
    public function setSize($size) {
        $this->size = $size;
        return $this;
    }
    
    public function setDateAdded($date_added) {
        $this->date_added = $date_added;
        return $this;
    }
    
    public function setHash($hash) {
        $this->hash = $hash;
        return $this;
    }
    
    public function setVector($vector) {
        $this->vector = $vector;
        return $this;
    }
    
    public function setResult($result) {
        $this->result = $result;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getExtension() {
        return $this->extension;
    }
    
    public function getSize() {
        return $this->size;
    }
    
    public function getDateAdded() {
        return $this->date_added;
    }
    
    public function getHash() {
        return $this->hash;
    }
    
    public function getVector() {
        return $this->vector;
    }
    
    public function getResult() {
        return $this->result;
    }
}
