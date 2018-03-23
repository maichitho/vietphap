<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public function _initAutoloader() {
        require_once 'Util.php';
        require_once 'MapperUtil.php';
        require_once "ControllerUtils.php";
        require_once "UploadHandler.php";
        require_once "FileUtil.php";
        require_once "DateTimeUtil.php";
        require_once "ViewerUtils.php";
//        require_once 'PHPExcel/IOFactory.php';
    }

    public function _initTimeZone() {
        date_default_timezone_set("Asia/Jakarta");
    }

    public function _initPlugins() {
        require_once 'plugins/Plugin_Layout.php';
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_Layout());
    }

    public function _initServices() {
        require_once 'mapper/Services.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace(array('SA_'));
        ControllerUtils::initMultiLanguage();
    }

    public function _initRouters() {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        //Home page
        $homePageRouter = new Zend_Controller_Router_Route('', array(
            'module' => 'home',
            'controller' => 'home',
            'action' => 'index'));
        $router->addRoute('home-page', $homePageRouter);

        // view an article which are an entry, a category or categories
        $viewEntryRouter = new Zend_Controller_Router_Route_Regex(
                '([\.a-z0-9A-Z\/-]+)', array(
            "module" => "home",
            'controller' => 'news',
            'action' => 'view-article'
                ), array(
            1 => 'rewriteUrl'
                ), '%s'
        );
        $router->addRoute('view-article', $viewEntryRouter);

        // view an article which are an entry, a category or categories with paging
        $viewEntryPageRouter = new Zend_Controller_Router_Route_Regex(
                '([\.a-z0-9A-Z\/-]+)?(pageId=\d+)', array(
            "module" => "home",
            'controller' => 'news',
            'action' => 'view-article'
                ), array(
            1 => 'rewriteUrl',
            2 => 'pageId'
                ), '%s%s'
        );
        $router->addRoute('view-article-page', $viewEntryPageRouter);

        // view question list
        $listAllQaRouter = new Zend_Controller_Router_Route('/hoi-dap/*', array(
            'module' => 'home',
            'controller' => 'qa',
            'action' => 'list'));
        $router->addRoute('list-all-qa', $listAllQaRouter);
        
         // view question list
        $InsertQaRouter = new Zend_Controller_Router_Route('/tao-qa/*', array(
            'module' => 'home',
            'controller' => 'qa',
            'action' => 'async-insert-qa'));
        $router->addRoute('async-insert-qa', $InsertQaRouter);


//        $listAsyncQaRouter = new Zend_Controller_Router_Route('/hoi-dap/*', array(
//            'module' => 'home',
//            'controller' => 'qa',
//            'action' => 'list'));
//        $router->addRoute('list-all-qa', $listAsyncQaRouter);

        // view search list
        $searchRouter = new Zend_Controller_Router_Route('/search/*', array(
            'module' => 'home',
            'controller' => 'news',
            'action' => 'search-list'));
        $router->addRoute('search', $searchRouter);

        // direct to system module
        $viewSystemRouter = new Zend_Controller_Router_Route(
                'system/:controller/:action/*', array(
            "module" => "system"));
        $router->addRoute('system', $viewSystemRouter);

        $viewSystemDefaultRouter = new Zend_Controller_Router_Route(
                'system', array(
            "module" => "system"));
        $router->addRoute('default', $viewSystemDefaultRouter);
    }

}
