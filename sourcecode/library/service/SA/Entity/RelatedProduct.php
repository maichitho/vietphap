<?php
/**
 * Object represents table 'do_related_product'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_RelatedProduct{
    
	private $id;
	private $productId;
	private $relatedProductId;
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

	public function getRelatedProductId() {
		return $this->relatedProductId;
	}

	public function setRelatedProductId( $relatedProductId ) {
		$this->relatedProductId = $relatedProductId;
		return $this;
	}

	public function getCreateTime() {
		return $this->createTime;
	}

	public function setCreateTime( $createTime ) {
		$this->createTime = $createTime;
		return $this;
	}

		
}
?>