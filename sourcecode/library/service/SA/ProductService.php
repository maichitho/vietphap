<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductService
 *
 * @author Sonlx
 * @created 2014-05-07 08:52:45
 */
interface SA_ProductService {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    //<editor-fold desc="Product" defaultstate="collapsed">
    /**
     * 
     * @param type $id
     * @param type $languageCode
     * @return DO_Entity_Product
     */
    public function get($id, $languageCode);

    /**
     * 
     * @param DO_Entity_Product $product
     * @require
     *      code, createTime, updateTime
     * @option
     *      - productImages (create new the list of image of product)
     *      - productConsts (create new the list of link(product N const))
     * @return 
     *      - id of new product
     */
    public function create(SA_Entity_Product $product);

    /**
     * 
     * @param type $id (id of product)
     */
    public function delete($id);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @option
     *      productName, productCode,
     *      categoryId:,
     *      productType,     
     *      languageCode,
     *      relateOfProductId,
     *      orders: array(
     *          array(
     *              'column' => ?,  (column in ["title", "create_time"])
     *              'type' => ?     (type in ["acs", "desc"])
     *          )
     *          ex: $filter['orders'] = array();
     *              $orderItem = array( 'column' => 'title' , 'type' => 'asc' );
     *              $filter['orders'][] = $orderItem;
     *      ),
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of PS_Entity_Product
     *      - case2: Interger
     */
    public function find($filter);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @option
     *      productName, productCode,
     *      categoryIds:array(id, type),
     *      productType,     
     *      languageCode,
     *      relateOfProductId,
     *      orders: array(
     *          array(
     *              'column' => ?,  (column in ["title", "create_time"])
     *              'type' => ?     (type in ["acs", "desc"])
     *          )
     *          ex: $filter['orders'] = array();
     *              $orderItem = array( 'column' => 'title' , 'type' => 'asc' );
     *              $filter['orders'][] = $orderItem;
     *      ),
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of PS_Entity_Product
     *      - case2: Interger
     */
    public function find1($filter);

    /**
     * 
     * @param DO_Entity_Product $product
     */
    public function update(SA_Entity_Product $product);

    /**
     * 
     * @param type $productId
     * @param type $relatedProductId     
     */
    public function addRelation($productId, $relatedProductId);

    /**
     * 
     * @param type $productId
     * @param type $relatedProductId
     */
    public function removeRelation($productId, $relatedProductId);

    /**
     * 
     * @param type $productId     
     */
    public function removeAllRelation($productId);

    public function assignProductToCategory($productId, $categoryId);

    public function unassignProductToCategory($productId, $categoryId);

    public function unassignAllProductCategories($productId);

    //</editor-fold>
    //<editor-fold desc="Product Category" defaultstate="collapsed">
    /**
     * 
     * @param type $categoryId
     * @return array of DO_Entity_ProductCategory
     */
    public function getCategory($categoryId, $languageCode);

    /**
     * 
     * @param DO_Entity_ProductCategory $category
     * @require
     *      - name, orderNumber, createTime, updateTime
     * @return
     *      - id of new product category
     */
    public function createCategory(SA_Entity_ProductCategory $category);

    /**
     * 
     * @param type $categoryId
     * @tutorial only can delete category when category haven't any child (product)
     */
    public function deleteCategory($categoryId);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @options
     *      orders: array(
     *          array(
     *              'column' => ?,  (column in ["title", "create_time"])
     *              'type' => ?     (type in ["acs", "desc"])
     *          )
     *          ex: $filter['orders'] = array();
     *              $orderItem = array( 'column' => 'title' , 'type' => 'asc' );
     *              $filter['orders'][] = $orderItem;
     *      ),
     *      productId,
     *      type, 
     *      languageCode,
     *      parentId,
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of DO_Entity_ProductCategory
     *      - case2: Interger
     */
    public function findCategories($filter);

    /**
     * 
     * @param type $id
     * @param type $languageCode
     * @return type $retVal - Array of Category;
     */
    public function getCategoryTreeForNavigation($id, $languageCode);

    /**
     * 
     * @param DO_Entity_ProductCategory $category
     */
    public function updateCategory(SA_Entity_ProductCategory $category);

    /**
     * 
     * @param type $id
     */
    public function upCategory($id);

    /**
     * 
     * @param type $id
     */
    public function downCategory($id);

    //</editor-fold>
    //<editor-fold desc="Product Image" defaultstate="collapsed">
    /**
     * 
     * @param type $id
     * @return DO_Entity_ProductImage
     */
    public function getProductImage($id);

    /**
     * 
     * @param SA_Entity_ProductImage $productImage
     * @return id of new product image     
     */
    public function createProductImage(SA_Entity_ProductImage $productImage);
    
    /**
     * 
     * @param SA_Entity_ProductImage $productImage
     * @return id of update product image     
     */
    public function updateProductImage(SA_Entity_ProductImage $productImage);

    /**
     * 
     * @param type $id     
     */
    public function deleteProductImage($id);

    /**
     * 
     * @param type $productId     
     */
    public function deleteProductImageByProductId($productId);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @options
     *      productId, 
     *      type,
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of DO_Entity_ProductConst
     *      - case2: Interger
     */
    public function findProductConsts($filter);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @options
     *      productId, 
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of DO_Entity_ProductImage
     *      - case2: Interger
     */
    public function findProductImages($productId);
    //</editor-fold>
    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}

?>
