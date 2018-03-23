<?php

/**
 * Object represents table 'sa_category'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_Category {

    const TYPE_GROUP_PRODUCT = "group";
    const TYPE_ENTRY = "entry";
    const TYPE_SERVICE = "service";
    const TYPE_CATEGORIES = "categories";
    const TYPE_ONE_CATEGORY = "one_category";
    const TYPE_MAIN = "main";
    const TYPE_CHILD = "child";
    const TYPE_SUPPORT = "support";
    const TYPE_QA = "qNa";
    const TYPE_SUPPORT_YAHOO = "Yahoo";
    const TYPE_SUPPORT_SKYPE = "Skype";
    const TYPE_TESTIMONIAL = "testimonial";

    private $id;
    private $parentId;
    private $code;
    private $name;
  
    private $seoKeyword;
    private $seoDescription;
    private $description;
    private $imagePath;
    private $iconPath;
    private $type;
    private $orderNumber;
    private $createTime;
    private $updateTime;
    private $headerMenu;
    private $footerMenu;
    //Transience
    private $entries;

    public function getEntries() {
        return $this->entries;
    }

    public function setEntries($entries) {
        $this->entries = $entries;
        return $this;
    }
      public function getSeoKeyword() {
        return $this->seoKeyword;
    }

    public function setSeoKeyword($seoKeyword) {
        $this->seoKeyword = $seoKeyword;
        return $this;
    }
    
       public function getSeoDescription() {
        return $this->seoDescription;
    }

    public function setSeoDescription($seoKeyword) {
        $this->seoDescription = $seoKeyword;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function setParentId($parentId) {
        $this->parentId = $parentId;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
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

    public function getIconPath() {
        return $this->iconPath;
    }

    public function setIconPath($iconPath) {
        $this->iconPath = $iconPath;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getHeaderMenu() {
        return $this->headerMenu;
    }

    public function setHeaderMenu($headerMenu) {
        $this->headerMenu = $headerMenu;
        return $this;
    }

    public function getFooterMenu() {
        return $this->footerMenu;
    }

    public function setFooterMenu($footerMenu) {
        $this->footerMenu = $footerMenu;
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