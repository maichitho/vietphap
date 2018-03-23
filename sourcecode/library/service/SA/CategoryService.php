<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author ThoMC
 */
interface SA_CategoryService {

    /**
     * 
     * @param type $categoryId
     * @return SA_Entity_Category
     */
    public function get($categoryId);

    public function getCode($categoryCode);

    public function getRewriteUrl($rewriteUrl);

    /**
     * 
     * @param SA_Entity_Category $category
     * @require
     *      - name, orderNumber, createTime
     * @return
     *      - id of new category
     */
    public function create(SA_Entity_Category $category);

    /**
     * 
     * @param type $categoryId
     * @tutorial only can delete category when category haven't any child
     */
    public function delete($categoryId);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "recordCount" (require: not exist pageId & pageSize)
     *              + metric = "pageCount" (require: pageSize)
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
     *      productId, entryId, supplierId, customerId
     *      type,
     *      parentId, parentCode
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Category
     *      - case2: Interger
     */
    public function find($filter);

    /**
     * 
     * @param SA_Entity_Category $category
     */
    public function update(SA_Entity_Category $category);

    /**
     * 
     * @param type $id
     */
    public function up($id);

    /**
     * 
     * @param type $id
     */
    public function down($id);

    /**
     * 
     * @param type $parentCateId
     */
    public function getMaxOrderByParentId($parentCateId);

    public function serialize($categories, $currentCategory = NULL, $level = 0);

    public function createLocation(SA_Entity_Location $location);

    public function deleteLocation($locationId);

    public function downLocation($id);

    public function findLocation($filter);

    public function getLocation($locationId);

    public function updateLocation($location);

    public function upLocation($id);
}
