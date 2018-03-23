<?php

/**
 * Object represents table 'sa_workshop'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_Workshop {

    private $id;
    private $imageUrl;
    private $title;
    private $content;
    private $quantity;
    private $joinDocUrl;
    private $registerDocUrl;
    private $address;
    private $startDate;
    private $mapUrl;
    private $time;
    private $createTime;
    private $updateTime;
    private $isTop;

    public function getJoinDocUrl() {
        return $this->joinDocUrl;
    }

    public function setJoinDocUrl($joinDocUrl) {
        $this->joinDocUrl = $joinDocUrl;
        return $this;
    }

    public function getRegisterDocUrl() {
        return $this->registerDocUrl;
    }

    public function setRegisterDocUrl($registerDocUrl) {
        $this->registerDocUrl = $registerDocUrl;
        return $this;
    }

    public function getIsTop() {
        return $this->isTop;
    }

    public function setIsTop($isTop) {
        $this->isTop = $isTop;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    /**
     * 
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    public function getMapUrl() {
        return $this->mapUrl;
    }

    public function setMapUrl($mapUrl) {
        $this->mapUrl = $mapUrl;
        return $this;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
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