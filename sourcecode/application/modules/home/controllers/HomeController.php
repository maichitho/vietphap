<?php

class Home_HomeController extends Zend_Controller_Action {

    public function init() {

        ControllerUtils::prepareMenuData($this->view);
    }

    protected function get_server_var($id) {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    public function indexAction() {
//        $frontendOptions = array(
//            'lifetime' => 300,                   // cache lifetime of 30 seconds
//            'automatic_serialization' => false  // this is the default anyways
//        );
//
//        $backendOptions = array('cache_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')) . '/cache/');
//
//        try{
//            $cache = Zend_Cache::factory('Output',
//                'File',
//                $frontendOptions,
//                $backendOptions);
//        } catch (Exception $e){
//            error_log("Looooooi:".$e->getMessage());
//        }
//        if(!$cache->start('mypage')) {

            // output as usual:

            ControllerUtils::prepareMenuData($this->view);

            $menu = Services::createConfigurationService()->getMenuByRewriteUrl("/");

            if ($menu->getParentId() != null && $menu->getParentId() > 0) {
                $this->view->selectedMenuId = $menu->getParentId();
            } else {
                $this->view->selectedMenuId = $menu->getId();
            }

            $this->view->isHome = true;

//Bài báo top
            $hotEntries = Services::createEntryService()->
            find(array("pageSize" => 3, "pageId" => 0, "top" => TRUE, "type" =>
                array(SA_Entity_Category::TYPE_CHILD, SA_Entity_Category::TYPE_ENTRY)));
            foreach ($hotEntries as $en) {
                $hotEntryIds[] = $en->getId();
            }
            if (count($hotEntries) > 0) {
                $topEntry = $hotEntries[0];
            }
            unset($hotEntries[0]);
//categories
            $categoryService = Services::createCategoryService();
            $filterCat = array("pageSize" => 0,
                "pageId" => 0,
                "type" => SA_Entity_Category::TYPE_MAIN);
            $mainCate = $categoryService->find($filterCat);
//        $mainCatMap ;
//categories
            $filterQACat = array("pageSize" => 0,
                "pageId" => 0,
                "type" => SA_Entity_Category::TYPE_QA);
            $qaCates = $categoryService->find($filterQACat);

//Subcategory
            $conditions = array("pageSize" => 0,
                "pageId" => 0,
                "type" => SA_Entity_Category::TYPE_CHILD,
                "order" => array("order_number", "asc"));
            $subCateList = $categoryService->find($conditions);
            foreach ($mainCate as $m) {
                $mainMap = MapperUtil::mapObject($m);
                $subCList = $this->findSub($m->getId(), $subCateList);
                $subCMap = array();
                foreach ($subCList as $s) {
                    //Lay danh sach bai viet
                    $filter = array("pageSize" => 4,
                        "pageId" => 0,
                        "categoryId" => $s->getId(),
                        "ignoreEntryIds" => $hotEntryIds);
                    $sub = MapperUtil::mapObject($s);
                    $sub["entries"] = MapperUtil::mapObjects(Services::createEntryService()->find($filter));

                    $menuFilter = array("pageSize" => 0,
                        "pageId" => 0,
                        "linkId" => $s->getId());
                    $menus = Services::createConfigurationService()->findMenus($menuFilter);
                    if (count($menus) > 0) {
                        $sub["rewriteUrl"] = $menus[0]->getRewriteUrl();
                    }
                    $subCMap[] = $sub;
                }

                $mainMap["subCatMap"] = $subCMap;
                $menuFilter = array("pageSize" => 0,
                    "pageId" => 0,
                    "linkId" => $m->getId());
                $menus = Services::createConfigurationService()->findMenus($menuFilter);
                if (count($menus) > 0) {
                    $mainMap["rewriteUrl"] = $menus[0]->getRewriteUrl();
                }
                $mainCatMap[] = $mainMap;
            }

            $this->view->mainCatMap = $mainCatMap;
            $hotEntriesMap = ControllerUtils::mapEntrysHaveCategory($hotEntries, $subCateList);
            $topEntry = ControllerUtils::mapEntryHaveCategory($topEntry, $subCateList);
            // get Tin tức
            $tintucId = Services::createConfigurationService()->get("category.id.tintuc")->getValue();
            $tintuc = MapperUtil::mapObject(Services::createCategoryService()->get($tintucId));

            $filter = array("pageSize" => 4,
                "pageId" => 0,
                "categoryId" => $tintucId,
                "ignoreEntryIds" => $hotEntryIds);
            $tintuc["entries"] = MapperUtil::mapObjects(Services::createEntryService()->find($filter));
            $menuFilter = array("pageSize" => 0,
                "pageId" => 0,
                "linkId" => $tintucId);
            $menus = Services::createConfigurationService()->findMenus($menuFilter);
            if (count($menus) > 0) {
                $tintuc["rewriteUrl"] = $menus[0]->getRewriteUrl();
            }
            $this->view->tintuc = $tintuc;


            // get Hỏi đáp
            $hoidapId = Services::createConfigurationService()->get("category.id.qa")->getValue();
            $hoidap = MapperUtil::mapObject(Services::createCategoryService()->get($hoidapId));

            $filter = array("pageSize" => 8,
                "pageId" => 0,
                "categoryId" => $hoidapId,
                "ignoreEntryIds" => $hotEntryIds, "display" => 1);
            $hoidap["entries"] = MapperUtil::mapObjects(Services::createEntryService()->find($filter), MapperUtil::PROPERTY_TYPE_CAMEL, MapperUtil::DATE_FORMAT);
            $menuFilter = array("pageSize" => 0,
                "pageId" => 0,
                "linkId" => $hoidapId);
            $menus = Services::createConfigurationService()->findMenus($menuFilter);
            if (count($menus) > 0) {
                $hoidap["rewriteUrl"] = $menus[0]->getRewriteUrl();
            }
            $this->view->hoidap = $hoidap;

            //get testimonials
            $filterCat = array("pageSize" => 0,
                "pageId" => 0,
                "type" => SA_Entity_Category::TYPE_TESTIMONIAL);
            $testimonials = $categoryService->find($filterCat);
            $this->view->testimonials = ControllerUtils::mapTestimonials($testimonials);


            $this->getSupporter();
            // get inner menu
            $this->getInnerMenu();


            $this->view->hotEntries = $hotEntriesMap;
            $this->view->topEntry = $topEntry;

            $this->renderScript("home/index.phtml");

//            $cache->save($this->getResponse()->getBody(), 'myresult');
//            $cache->end(); // the output is saved and sent to the browser
//        }else{
//            try{
//                $this->renderScript("../../../public/cache/zend_cache---myresult");
//            } catch (Exception $e){
//                error_log("Looooooi:".$e->getMessage());
//            }
//
//        }



    }

    private function findSub($mainId, $subCateList) {
        $ret = array();
        foreach ($subCateList as $sub) {
            if ($sub->getParentId() == $mainId) {
                $ret[] = $sub;
            }
        }
        return $ret;
    }

    public function contactAction() {
        $this->renderScript("home/contact.phtml");
    }

    private function getSupporter() {
        $filter = array("keyPrefix" => "home.support");
        $homesupport = Services::createConfigurationService()->find($filter);
        $expert_img = ControllerUtils::getParamByKey($homesupport, "home.support.expert_img");
        $expert_position = ControllerUtils::getParamByKey($homesupport, "home.support.expert_position");
        $expert_name = ControllerUtils::getParamByKey($homesupport, "home.support.expert_name");
        $expert_des = ControllerUtils::getParamByKey($homesupport, "home.support.expert_des");
        $this->view->expert_img = $expert_img->getValue();
        $this->view->expert_position = $expert_position->getValue();
        $this->view->expert_name = $expert_name->getValue();
        $this->view->expert_des = $expert_des->getValue();
    }

//Get Inner Menu
    private function getInnerMenu() {
        $filterright = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_INNER_RIGHT,
            "status" => '1',
            "order" => array("order_number", "asc"));

        $menus = Services::createConfigurationService()->findMenus($filterright);
        foreach ($menus as $menu) {
            if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_NEWS) {
                // get top news that viewed much
                $filter = array("pageSize" => 6,
                    "pageId" => 0,
                    "display" => '1',
                    "orders" => array(array("column" => "total_views", "type" => "desc")));
                $this->view->topViewEntries = Services::createEntryService()->find($filter);
                break;
            }
        }

        $this->view->menuInnerRight = $menus;
    }

//Get qa
    private function getQA() {
        $filter = array("pageId" => 0,
            "pageSize" => Services::createConfigurationService()->get('qa.home.page.size')->getValue(),
            "type" => array(SA_Entity_Category::TYPE_QA),
            "display" => "1",
            "orders" => array(array("column" => "is_top", "type" => "desc")));

        $qas = Services::createEntryService()->find($filter);
//Map data
        $qasMap = array();
        foreach ($qas as $qa) {
            $qasMap[] = MapperUtil::mapObject($qa, null, Util::$DATE_FORMAT);
        }

        $this->view->qas = $qasMap;
    }

}
