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
interface SA_WorkshopService {


    /**
     * 
     * @param type $workshopId
     * @return SA_Entity_Workshop
     */
    public function get($workshopId);

    /**
     * 
     * @param SA_Entity_Workshop $workshop
     * @require
     *      - name, orderNumber, createTime
     * @return
     *      - id of new workshop
     */
    public function create(SA_Entity_Workshop $workshop);

    /**
     * 
     * @param type $workshopId
     * @tutorial only can delete workshop when workshop haven't any child
     */
    public function delete($workshopId);

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "recordCount" (require: not exist pageId & pageSize)
     *              + metric = "pageCount" (require: pageSize)
     * @options
     *      top, title
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Workshop
     *      - case2: Interger
     */
    public function find($filter);

    /**
     * 
     * @param SA_Entity_Workshop $workshop
     */
    public function update(SA_Entity_Workshop $workshop);

    
}
