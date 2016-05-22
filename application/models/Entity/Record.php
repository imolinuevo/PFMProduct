<?php

namespace Entity;

/**
 *
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
     * @Column(type="string", length=255, nullable=false)
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
     * @Column(type="integer", nullable=false)
     */
    protected $user_id;

    public function __construct($name, $extension, $size, $date_added, $hash, $vector, $user_id) {
        $this->name = $name;
        $this->extension = $extension;
        $this->size = $size;
        $this->date_added = $date_added;
        $this->hash = $hash;
        $this->vector = $vector;
        $this->user_id = $user_id;
    }

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
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function getId() {
        return $this->id;
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

    public function getFormattedSize() {
        $base = log($this->size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), 2) . ' ' . $suffixes[floor($base)];
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
    
    public function getUserId() {
        return $this->user_id;
    }

}
