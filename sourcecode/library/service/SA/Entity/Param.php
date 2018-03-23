<?php

/**
 * Object represents table 'do_param'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_Param {

    const TYPE_TEXT = "text";
    const TYPE_IMG = "img";
    const TYPE_TEXTAREA = "textarea";
    const TYPE_HTML = "html";
    const TYPE_CHECKBOX = "checkbox";
    const PARAM_TYPE_GENERAL = "general";
    const PARAM_TYPE_IMAGE = "image";
    const PARAM_TYPE_MAP = "map";
    const PARAM_TYPE_SNETWORK = "snetwork";
    const PARAM_TYPE_SEO = "seo";

    private $id;
    private $key2;
    private $value;
    private $description;
    private $createTime;
    private $updateTime;
    private $type;
    private $paramType;
    private $orderNumber;

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getKey2() {
        return $this->key2;
    }

    public function setKey2($key) {
        $this->key2 = $key;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getParamType() {
        return $this->paramType;
    }

    public function setParamType($paramType) {
        $this->paramType = $paramType;
        return $this;
    }

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
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