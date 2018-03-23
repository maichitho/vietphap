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
interface SA_DrugstoreService {

    /**
     * 
     * @param type $id
     * @return SA_Entity_Drugstore
     */
    public function get($id);

    /**
     * 
     * @param SA_Entity_Drugstore $entry
     * @return id of new drugstore
     */
    public function create(SA_Entity_Drugstore $drugstore);

    /**
     * 
     * @param SA_Entity_Drugstore $drugstore     
     */
    public function update(SA_Entity_Drugstore $drugstore);

    /**
     * 
     * @param type $id
     */
    public function delete($id);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "recordCount" (require: not exist pageId & pageSize)
     *              + metric = "pageCount" (require: pageSize)
     * @options
     *      categoryId, creatorId, keyword     
     *      categoryType, ignoreSupplierEntry
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Drugstore
     *      - case2: Integer
     */
    public function find($filter);

    public function getMaxOrderNumber();

    public function deleteByLocation($cityId, $districtId);
}
