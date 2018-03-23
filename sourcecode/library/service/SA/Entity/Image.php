<?php
/**
 * Object represents table 'sa_image'
 *
 * @author: Sililab
 * @date: 2013-12-31 07:44	 
 */
class SA_Entity_Image{
    
	private $id;
	private $galleryId;
	private $title;
	private $description;
	private $url;
        private $thumbnailUrl;
	private $createTime;
	private $updateTime;
        private $name;
        private $extension;
        private $type;
        
        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
             return $this;
        }

        public function getExtension() {
            return $this->extension;
        }

        public function setExtension($extension) {
            $this->extension = $extension;
             return $this;
        }

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

	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}

	public function getGalleryId() {
		return $this->galleryId;
	}

	public function setGalleryId( $galleryId ) {
		$this->galleryId = $galleryId;
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

	public function getUrl() {
		return $this->url;
	}

	public function setUrl( $url ) {
		$this->url = $url;
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