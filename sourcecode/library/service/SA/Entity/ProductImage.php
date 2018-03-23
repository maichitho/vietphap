<?php
/**
 * Object represents table 'sa_product_image'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_ProductImage{
    
	private $id;
	private $productId;
	private $name;
        private $thumbnailUrl;
	private $url;
	private $isRepresentation;
	private $createTime;
        

	public function getId() {
		return $this->id;
	}

	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}

	public function getProductId() {
		return $this->productId;
	}

	public function setProductId( $productId ) {
		$this->productId = $productId;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName( $name ) {
		$this->name = $name;
		return $this;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl( $url ) {
		$this->url = $url;
		return $this;
	}

	public function getIsRepresentation() {
		return $this->isRepresentation;
	}

	public function setIsRepresentation( $isRepresentation ) {
		$this->isRepresentation = $isRepresentation;
		return $this;
	}

	public function getCreateTime() {
		return $this->createTime;
	}

	public function setCreateTime( $createTime ) {
		$this->createTime = $createTime;
		return $this;
	}

	public function getThumbnailUrl() {
		return $this->thumbnailUrl;
	}

	public function setThumbnailUrl( $thumbnailUrl) {
		$this->thumbnailUrl = $thumbnailUrl;
		return $this;
	}
	
}
?>