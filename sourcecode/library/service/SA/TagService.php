<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Phult
 */
interface SA_TagService {

    /**
     * 
     * @param type $id
     * @return SA_Entity_Tag
     */
    public function get( $id );

    /**
     * 
     * @param SA_Entity_Tag $tag
     * @return id of new entry
     */
    public function create( SA_Entity_Tag $tag );

    /**
     * 
     * @param SA_Entity_Tag $tag     
     */
    public function update( SA_Entity_Tag $tag );

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
     *      keyword
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Tag
     *      - case2: Interger
     */
    public function find( $filter );
}
