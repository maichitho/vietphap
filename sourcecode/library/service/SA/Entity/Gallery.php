<?php

/**
 * Object represents table 'sa_gallery'
 *
 * @author: Sililab
 * @date: 2013-12-31 07:44	 
 */
class SA_Entity_Gallery {

    private $id;
    private $categoryId;
    private $title;
    private $description;
    private $thumbnailUrl;
    private $createTime;
    private $updateTime;
    private $isTop;
    
    //extra
    private $images;
    
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

    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }

    public function getImages() {
        return $this->images;
    }

    public function setImages( $images ) {
        $this->images = $images;
        return $this;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function setCategoryId( $categoryId ) {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle( $title ) {
        $this->title = $title;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription( $description ) {
        $this->description = $description;
        return $this;
    }

    public function getThumbnailUrl() {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl( $thumbnailUrl ) {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    public function getCreateTime() {
        return $this->createTime;
    }

    public function setCreateTime( $createTime ) {
        $this->createTime = $createTime;
        return $this;
    }

    public function getUpdateTime() {
        return $this->updateTime;
    }

    public function setUpdateTime( $updateTime ) {
        $this->updateTime = $updateTime;
        return $this;
    }

}

?>