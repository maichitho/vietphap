<?php

/**
 * Object represents table 'do_product'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_Product {

    const STATUS_NO_PRODUCTS = "no-products";
    const STATUS_STILL_REMAIN = "still-remain";

    // common fields
    private $id;
    private $code;
    private $name;
    private $type;
    private $listPrice;
    private $salePrice;
    private $currencyCode;
    private $status;
    private $quantity;
    private $isShow;
    private $isNew;
    private $creatorId;
    private $description;
    private $createTime;
    private $updateTime;
    private $size;
    private $origin;
    private $color;
    private $thumbnailUrl;
    private $orderNumber;
    //Extra for create (not for update)
    private $productConsts;
    private $productImages;
    //Extra for show list

    private $languageCode;
    private $comment;
    private $evaluation;
    private $feature;
    private $technique;
    private $model;
    //UI
    private $providerName;
    private $groupName;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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

    public function getListPrice() {
        return $this->listPrice;
    }

    public function setListPrice($listPrice) {
        $this->listPrice = $listPrice;
        return $this;
    }

    public function getSalePrice() {
        return $this->salePrice;
    }

    public function setSalePrice($salePrice) {
        $this->salePrice = $salePrice;
        return $this;
    }

    public function getCurrencyCode() {
        return $this->currencyCode;
    }

    public function setCurrencyCode($currencyCode) {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId) {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    public function getIsShow() {
        return $this->isShow;
    }

    public function setIsShow($isShow) {
        $this->isShow = $isShow;
        return $this;
    }

    public function getIsNew() {
        return $this->isNew;
    }

    public function setIsNew($isNew) {
        $this->isNew = $isNew;
        return $this;
    }

    public function getCreatorId() {
        return $this->creatorId;
    }

    public function setCreatorId($creatorId) {
        $this->creatorId = $creatorId;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getCreateTime() {
        return $this->createTime;
    }

    public function setCreateTime($createTime) {
        $this->createTime = $createTime;
        return $this;
    }

    public function getUpdateTime() {
        return $this->updateTime;
    }

    public function setUpdateTime($updateTime) {
        $this->updateTime = $updateTime;
        return $this;
    }

    public function getOrigin() {
        return $this->origin;
    }

    public function setOrigin($origin) {
        $this->origin = $origin;
        return $this;
    }

    public function getThumbnailUrl() {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl($thumbnailUrl) {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    public function getProductConsts() {
        return $this->productConsts;
    }

    public function setProductConsts($productConsts) {
        $this->productConsts = $productConsts;
        //extra
        if ($productConsts != null && count($productConsts) > 0) {
            $this->type = "";
            $this->color = "";
            foreach ($productConsts as $consts) {
                /* @var $consts DO_Entity_ProductConst */
                if ($consts->getType() == DO_Entity_ProductConst::TYPE_PRODUCT_TYPE) {
                    $this->type .= $consts->getName() . ", ";
                } elseif ($consts->getType() == DO_Entity_ProductConst::TYPE_COLOR) {
                    $this->color .= $consts->getName() . ", ";
                }
            }
        }
        return $this;
    }

    public function getProductImages() {
        return $this->productImages;
    }

    public function setProductImages($productImages) {
        $this->productImages = $productImages;
        return $this;
    }

    public function getSize() {
        return $this->size;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function getColor() {
        return $this->color;
    }

    public function getLanguageCode() {
        return $this->languageCode;
    }

    public function setLanguageCode($languageCode) {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment($comment) {
        $this->comment = $comment;
        return $this;
    }

    public function getEvaluation() {
        return $this->evaluation;
    }

    public function setEvaluation($evaluation) {
        $this->evaluation = $evaluation;
        return $this;
    }

    public function getFeature() {
        return $this->feature;
    }

    public function setFeature($feature) {
        $this->feature = $feature;
        return $this;
    }

    public function getTechnique() {
        return $this->technique;
    }

    public function setTechnique($technique) {
        $this->technique = $technique;
        return $this;
    }

    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;
        return $this;
    }

    public function getOrderNumber() {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getProviderName() {
        return $this->providerName;
    }

    public function setProviderName($providerName) {
        $this->providerName = $providerName;
        return $this;
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function setGroupName($groupName) {
        $this->groupName = $groupName;
        return $this;
    }

}

?>