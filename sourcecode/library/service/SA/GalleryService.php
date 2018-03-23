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
interface SA_GalleryService {


    public function get( $id );

    public function create(SA_Entity_Gallery $entry );

    /**
     * 
     * @param SA_Entity_Gallery $entry     
     */
    public function update( SA_Entity_Gallery $entry );
    
    public function createImage(SA_Entity_Image $img);
    /**
     * Danh sách các ảnh cần insert
     * @param type $imgs
     */
    public function createImages($imgs);    
    public function updateImage(SA_Entity_Image $img);
    
    public function deleteImage($id);
    /**
     * 
     * @param type $id
     */
    public function getImage($id);
    
     /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "recordCount" (require: not exist pageId & pageSize)
     *              + metric = "pageCount" (require: pageSize)
     * @options
     *      galleryId, galleryIds     
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Image
     *      - case2: Integer
     */
    public function findImage($filter);

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
     *      top, title     
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Gallery
     *      - case2: Integer
     */
    public function find( $filter );
}
