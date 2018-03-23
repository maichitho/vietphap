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
interface SA_NotificationService {

    /**
     * 
     * @param type $id
     * @return SA_Entity_Notification
     */
    public function get($id);

    /**
     * 
     * @param SA_Entity_Notification $notification
     * @return id of new notification
     */
    public function create(SA_Entity_Notification $notification);

    /**
     * 
     * @param SA_Entity_Feedback $notification     
     */
    public function update(SA_Entity_Notification $notification);

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
     *      categoryType
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Feedback
     *      - case2: Integer
     */
    public function find($filter);
    
    /**
     * 
     * @param type $type
     */
    public function updateAll($type);
    
    /**
     * 
     */
    public function getNewCountByType();
}
