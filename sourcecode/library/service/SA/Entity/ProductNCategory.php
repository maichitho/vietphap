<?php

/**
 * Object represents table 'sa_product_n_const'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_ProductNCategory {

    private $id;
    private $productId;
    private $categoryId;

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

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function setCategoryId( $categoryId ) {
        $this->categoryId = $categoryId;
        return $this;
    }

}

?>