<?php

/**
 * Object represents table 'sa_location'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_Location {

    private $id;
    private $name;
    private $parentId;
    private $code;
    private $lft;
    private $rgt;
    private $description;
    private $displayName;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
        return $this;
    }

    public function getLft() {
        return $this->lft;
    }

    public function setLft($lft) {
        $this->lft = $lft;
        return $this;
    }

    public function getRgt() {
        return $this->rgt;
    }

    public function setRgt($rgt) {
        $this->rgt = $rgt;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function setParentId($parentId) {
        $this->parentId = $parentId;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

}

?>