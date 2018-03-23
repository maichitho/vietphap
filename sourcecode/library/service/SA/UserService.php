<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserService
 *
 * @author Sililab
 * @created 2014-05-06 17:53:17
 */
interface SA_UserService {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    /**
     * 
     * @param type $id
     * @return SA_Entity_User
     */
    public function get( $id );

    /**
     * 
     * @param type $username
     * @return SA_Entity_User
     */
    public function getByUsername( $username );

    /**
     * 
     * @param SA_Entity_User $user
     */
    public function create( SA_Entity_User $user );

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
     *      username, email, %keyword%
     *      type, supplierId
     *      createTimeFrom, createTimeTo (format: "Y-m-d h:m:s"), 
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_User
     *      - case2: array of Interger
     */
    public function find( $filter );

    /**
     * 
     * @param SA_Entity_User $user
     */
    public function update( SA_Entity_User $user );
    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
