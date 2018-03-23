<?php

/**
 * Object represents table 'sa_supplier'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_Drugstore {

    const STATUS_ACTIVE = "active";
    const STATUS_CLOSED = "closed";
    const STATUS_WAITING = "waiting";

    private $id;
    private $name;
    private $imagePath;
    private $status;
    private $address;
    private $email;
    private $phone;
    private $createTime;
    private $updateTime;
    private $description;
    private $cityId;
    private $districtId;
    private $orderNumber;
    private $uiCityName;
    private $uiDistrictName;

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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getCityId() {
        return $this->cityId;
    }

    public function setCityId($cityId) {
        $this->cityId = $cityId;
        return $this;
    }

    public function getDistrictId() {
        return $this->districtId;
    }

    public function setDistrictId($districtId) {
        $this->districtId = $districtId;
        return $this;
    }

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }
    
    public function getUiCityName() {
        return $this->uiCityName;
    }

    public function setUiCityName($uiCityName) {
        $this->uiCityName = $uiCityName;
        return $this;
    }
    
    public function getUiDistrictName() {
        return $this->uiDistrictName;
    }

    public function setUiDistrictName($uiDistrictName) {
        $this->uiDistrictName = $uiDistrictName;
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

}

?>