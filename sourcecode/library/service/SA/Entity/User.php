<?php

/**
 * Object represents table 'sa_user'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_User {

    const STATUS_ACTIVE = "active";
    const STATUS_INACTIVE = "inactive";
    const STATUS_BLOCK = "block";
    const TYPE_SUPPLIER = "supplier";
    const TYPE_CUSTOMER = "customer";
    const TYPE_ADMINISTRATOR = "admin";
    const TYPE_MANAGER = "manager";
    const TYPE_EDITOR = "editor";

    private $id;
    private $username;
    private $fullName;
    private $email;
    private $type;
    private $status;
    private $updateTime;
    private $createTime;
    private $password;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getFullName() {
        return $this->fullName;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * 
     * @return DateTime
     */
    public function getUpdateTime() {
        return $this->updateTime;
    }

    public function setUpdateTime($updateTime) {
        $this->updateTime = $updateTime;
        return $this;
    }

    /**
     * 
     * @return DateTime
     */
    public function getCreateTime() {
        return $this->createTime;
    }

    public function setCreateTime($createTime) {
        $this->createTime = $createTime;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

}

?>