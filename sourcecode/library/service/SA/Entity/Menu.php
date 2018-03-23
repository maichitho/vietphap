<?php

/**
 * Object represents table 'do_menu'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_Menu {

    const LINK_TYPE_MANUAL = "Manual";
    const LINK_TYPE_NEWS = "news"; // for display list of news
    const LINK_TYPE_ONE_CATEGORY = "one_category"; // for display list of news in one category
    const LINK_TYPE_CATEGORIES = "categories";
    const LINK_TYPE_ENTRY = "entry";
    const LINK_TYPE_ALBUM = "album";
    const LINK_TYPE_QA_CATEGORY = "qNa";
    const LINK_TYPE_QA = "qa";
    const LINK_TYPE_SERVICE_CATEGORY = "service_category";
    const LINK_TYPE_SERVICE = "service";
    const LINK_TYPE_EVENT = "event";
    const LINK_TYPE_IMAGE = "image";
    const LINK_TYPE_HTML = "html";
    const LINK_TYPE_VIDEO = "video";
   
    //---
    const STATUS_HIDE = "Hide";
    const STATUS_SHOW = "Show";
    //---
    const TYPE_HEADER = "header";
    const TYPE_FOOTER = "footer";
    const TYPE_RIGHT = "right";
    const TYPE_LEFT = "left";
    const TYPE_INNER_RIGHT = "inner right";

    //---
    private $id;
    private $parentId;
    private $code;
    private $type;
    private $name;
    private $description;
    private $linkId;
    private $linkType;
    private $linkUrl;
    private $logoPath;
    private $imagePath;
    private $htmlCode;
    private $status;
    private $orderNumber;
    private $createTime;
    private $updateTime;
    private $languageCode;
    private $rewriteUrl;

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

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
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

    public function getLinkId() {
        return $this->linkId;
    }

    public function setLinkId($linkId) {
        $this->linkId = $linkId;
        return $this;
    }

    public function getLinkType() {
        return $this->linkType;
    }

    public function setLinkType($linkType) {
        $this->linkType = $linkType;
        return $this;
    }

    public function getLinkUrl() {
        return $this->linkUrl;
    }

    public function setLinkUrl($linkUrl) {
        $this->linkUrl = $linkUrl;
        return $this;
    }

    public function getRewriteUrl() {
        return $this->rewriteUrl;
    }

    public function setRewriteUrl($rewriteUrl) {
        $this->rewriteUrl = $rewriteUrl;
        return $this;
    }

    public function getLogoPath() {
        return $this->logoPath;
    }

    public function setLogoPath($logoPath) {
        $this->logoPath = $logoPath;
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

    public function getHtmlCode() {
        return $this->htmlCode;
    }

    public function setHtmlCode($htmlCode) {
        $this->htmlCode = $htmlCode;
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

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getLanguageCode() {
        return $this->languageCode;
    }

    public function setLanguageCode($languageCode) {
        $this->languageCode = $languageCode;
        return $this;
    }

}
