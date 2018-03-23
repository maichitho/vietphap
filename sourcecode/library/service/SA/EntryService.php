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
interface SA_EntryService {

    /**
     * 
     * @param type $id
     * @return SA_Entity_Entry
     */
    public function get( $id );
    
    public function getByUrl( $url );

    /**
     * 
     * @param SA_Entity_Entry $entry
     * @return id of new entry
     */
    public function create( SA_Entity_Entry $entry );

    /**
     * 
     * @param SA_Entity_Entry $entry     
     */
    public function update( SA_Entity_Entry $entry );

    /**
     * 
     * @param type $id
     */
    public function delete( $id );

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
     *      relateOfEntryId
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Entry
     *      - case2: Integer
     */
    public function find( $filter );
    
    /**
     * Increase total_views when user visit an article
     */
//    public function updateViews();

    
    public function getMaxOrderNumber();
    
    /**
     * 
     * @param type $entryId
     * @param type $relatedEntryId     
     */
    public function addRelation($entryId, $relatedEntryId);

    /**
     * 
     * @param type $entryId
     * @param type $relatedEntryId
     */
    public function removeRelation($entryId, $relatedEntryId);

    /**
     * 
     * @param type $entryId     
     */
    public function removeAllRelation($entryId);
}
