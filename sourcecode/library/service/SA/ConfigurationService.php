<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigurationService
 *
 * @author Sililab
 */
interface SA_ConfigurationService {
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
     * @param SA_Entity_Param $param
     */
    public function create(SA_Entity_Param $param);

    /**
     * 
     * @param SA_Entity_Param $param
     */
    public function update(SA_Entity_Param $param);

    /**
     * 
     * @param type $key
     */
    public function delete($key);

    /**
     * 
     * @param SA_Entity_Param
     */
    public function get($key);

    /**
     * 
     * @param array filter
     * * @options
     *      keyPrefix,
     * @return 
     *      - case1: array of SA_Entity_Param
     */
    public function find($filter);

    /**
     * 
     * @param type $id
     * @param type $type
     */
    public function getCategoryTreeForNavigation($id, $type);

    /**
     * 
     * @param SA_Entity_Menu $menu
     */
    public function createMenu(SA_Entity_Menu $menu);

    /**
     * 
     * @param type $id
     */
    public function deleteMenu($id);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "recordCount" (require: not exist pageId & pageSize)
     *              + metric = "pageCount" (require: pageSize)
     * @options
     *      parentId,
     *      type,
     *      status,
     *      languageCode,
     *      linkId
     * @return 
     *      - case1: array of SA_Entity_Menu
     *      - case2: array of Interger
     */
    public function findMenus($filter);

    /**
     * 
     * @param type $id
     * @return list of SA_Entity_Menu
     */
    public function getMenu($id, $languageCode);
    
    /**
     * 
     * @param type $rewriteUrl
     * @return list of SA_Entity_Menu
     */
    public function getMenuByRewriteUrl($rewriteUrl);
    
    
    /**
     * 
     * @param SA_Entity_Menu $menu
     */
    public function updateMenu(SA_Entity_Menu $menu);

    /**
     * 
     * @param type $id
     */
    public function downMenu($id);

    /**
     * 
     * @param type $id
     */
    public function upMenu($id);

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
