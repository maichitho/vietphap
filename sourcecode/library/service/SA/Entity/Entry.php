<?php

/**
 * Object represents table 'sa_entry'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_Entry {

    private $id;
    private $title;
    private $description;
    private $content;
    private $creatorId;
    private $categoryId;
    private $imagePath;
    private $createTime;
    private $updateTime;
    private $facebookUrl;
    private $googleUrl;
    private $thumbnailUrl;
    private $tags;
    private $isTop;
    private $display;
    private $seoDescription;
    private $seoTitle;
    private $seoKeyword;
    private $rewriteUrl;
    private $orderNumber;
    private $asker;
    private $askerEmail;
    private $totalViews;

    public function getFacebookUrl() {
        return $this->facebookUrl;
    }

    public function setFacebookUrl($facebookUrl) {
        $this->facebookUrl = $facebookUrl;
        return $this;
    }

    public function getGoogleUrl() {
        return $this->googleUrl;
    }

    public function setGoogleUrl($googleUrl) {
        $this->googleUrl = $googleUrl;
        return $this;
    }

    public function getTags() {
        return $this->tags;
    }

    public function setTags($tags) {
        $this->tags = $tags;
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

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
     public function getTotalViews() {
        return $this->totalViews;
    }

    public function setTotalViews($totalViews) {
        $this->totalViews = $totalViews;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getCreatorId() {
        return $this->creatorId;
    }

    public function setCreatorId($creatorId) {
        $this->creatorId = $creatorId;
        return $this;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId) {
        $this->categoryId = $categoryId;
        return $this;
    }
    
     public function getThumbnailUrl() {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl($thumbnailUrl) {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    public function getSeoDescription() {
        return $this->seoDescription;
    }

    public function setSeoDescription($seoDescription) {
        $this->seoDescription = $seoDescription;
        return $this;
    }

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getSeoKeyword() {
        return $this->seoKeyword;
    }

    public function setSeoKeyword($seoKeyword) {
        $this->seoKeyword = $seoKeyword;
        return $this;
    }

    public function getSeoTitle() {
        return $this->seoTitle;
    }

    public function setSeoTitle($seoTitle) {
        $this->seoTitle = $seoTitle;
        return $this;
    }

    public function getRewriteUrl() {
        return $this->rewriteUrl;
    }

    public function setRewriteUrl($rewriteUrl) {
        $this->rewriteUrl = $rewriteUrl;
        return $this;
    }

    public function getDisplay() {
        return $this->display;
    }

    public function setDisplay($display) {
        $this->display = $display;
        return $this;
    }

    public function getAsker() {
        return $this->asker;
    }

    public function setAsker($asker) {
        $this->asker = $asker;
        return $this;
    }

    public function getAskerEmail() {
        return $this->askerEmail;
    }

    public function setAskerEmail($askerEmail) {
        $this->askerEmail = $askerEmail;
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