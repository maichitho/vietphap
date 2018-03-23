<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Services
 *
 * @author ThoMC
 * Apr 14, 2013 3:11:07 PM
 */
class Services {

    private static $authenService;
    private static $configurationService;
    private static $categoryService;
    private static $entryService;
    private static $userService;
    private static $tagService;
    private static $notificationService;
    private static $galleryService;
    private static $workshopService;
    private static $productService;
    private static $drugstoreService;

    /**
     * 
     * @return SA_ProductService
     */
    public static function createProductService() {
        if (Services::$productService == null) {
            Services::$productService = new SA_ProductMapper();
        }
        return Services::$productService;
    }

    /**
     * 
     * @return SA_AuthenticationService
     */
    public static function createAuthenticationService() {
        if (Services::$authenService == null) {
            Services::$authenService = new SA_AuthenticationMapper();
        }
        return Services::$authenService;
    }

    /**
     * 
     * @return SA_GalleryService
     */
    public static function createGalleryService() {
        if (Services::$galleryService == null) {
            Services::$galleryService = new SA_GalleryMapper();
        }
        return Services::$galleryService;
    }

    /**
     * 
     * @return SA_WorkshopService
     */
    public static function createWorkshopService() {
        if (Services::$workshopService == null) {
            Services::$workshopService = new SA_WorkshopMapper();
        }
        return Services::$workshopService;
    }

    public static function createNotificationService() {
        if (Services::$notificationService == null) {
            Services::$notificationService = new SA_NotificationMapper();
        }
        return Services::$notificationService;
    }

    /**
     * 
     * @return SA_ConfigurationService
     */
    public static function createConfigurationService() {
        if (Services::$configurationService == null) {
            Services::$configurationService = new SA_ConfigurationMapper();
        }
        return Services::$configurationService;
    }

    /**
     * 
     * @return SA_CategoryService
     */
    public static function createCategoryService() {
        if (Services::$categoryService == null) {
            Services::$categoryService = new SA_CategoryMapper();
        }
        return Services::$categoryService;
    }

    /**
     * 
     * @return SA_EntryService
     */
    public static function createEntryService() {
        if (Services::$entryService == null) {
            Services::$entryService = new SA_EntryMapper();
        }
        return Services::$entryService;
    }

    /**
     * 
     * @return SA_UserService
     */
    public static function createUserService() {
        if (Services::$userService == null) {
            Services::$userService = new SA_UserMapper();
        }
        return Services::$userService;
    }

    /**
     * 
     * @return SA_TagService
     */
    public static function createTagService() {
        if (Services::$tagService == null) {
            Services::$tagService = new SA_TagMapper();
        }
        return Services::$tagService;
    }

    /**
     * 
     * @return SA_DrugstoreService
     */
    public static function createDrugstoreService() {
        if (Services::$drugstoreService == null) {
            Services::$drugstoreService = new SA_DrugstoreMapper();
        }
        return Services::$drugstoreService;
    }

}

?>
