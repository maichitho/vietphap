<?php

class Home_NewsController extends Zend_Controller_Action {

    public function init() {
        ControllerUtils::prepareMenuData($this->view);
    }

    public function indexAction() {
        
    }

    private function viewEntry($rewriteUrl) {
        $entry = Services::createEntryService()->getByUrl($rewriteUrl);

        // get category
        $category = Services::createCategoryService()->get($entry->getCategoryId());

        $filterMenu = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_HEADER,
            "status" => '1',
            "linkId" => $entry->getCategoryId(),
            "order" => array("order_number", "asc"));

        $menus = Services::createConfigurationService()->findMenus($filterMenu);
//        $menu = Services::createConfigurationService()->getMenuByRewriteUrl($category->getRewriteUrl());
        if ($menus != NULL && count($menus)) {
            if ($menus[0]->getParentId() != null && $menus[0]->getParentId() > 0) {
                $this->view->selectedMenuId = $menus[0]->getParentId();
            } else {
                $this->view->selectedMenuId = $menus[0]->getId();
            }
        } else {
            $this->view->selectedMenuId = 0;
        }

        $this->view->entry = MapperUtil::mapObject($entry, MapperUtil::PROPERTY_TYPE_CAMEL, MapperUtil::DATE_FORMAT);
        $this->view->entry["categoryName"] = $category->getName();
        $this->view->categoryName = $category->getName();

        $this->getSupporter();
        // get inner menu
        $this->getInnerMenu();
		
		  $filterRelated = array("pageSize" => 6,
            "pageId" => 0,
            "relateOfEntryId" => $entry->getId());
        $this->view->suggestionEntries = MapperUtil::mapObjects(Services::createEntryService()->find($filterRelated), MapperUtil::PROPERTY_TYPE_CAMEL, MapperUtil::DATE_FORMAT);
		
		if(count($this->view->suggestionEntries)==0){

        // get older entries in the same category
        $filterRelated = array("pageSize" => 6,
            "pageId" => 0,
            "categoryId" => $entry->getCategoryId());
        $this->view->suggestionEntries = MapperUtil::mapObjects(Services::createEntryService()->find($filterRelated), MapperUtil::PROPERTY_TYPE_CAMEL, MapperUtil::DATE_FORMAT);
		}
//        $this->view->suggestionEntries = $suggestionEntriesMap;
        //Get Navigation
        $navigations = Services::createConfigurationService()->getCategoryTreeForNavigation($entry->getCategoryId(), SA_Entity_Menu::LINK_TYPE_ENTRY);
        $navigations[] = array("name" => $entry->getTitle(), "link" => "");
        $this->view->navigations = $navigations;
    }

    public function listOneCategory($params) {
        $this->listEntry($params);
        $filter = array("keyPrefix" => "system.category");
        $categoryConfig = Services::createConfigurationService()->find($filter);

        $this->getSupporter();
        // get inner menu
        $this->getInnerMenu();

        if (isset($params["categoryId"])) {
            if (ControllerUtils::getParamByKey($categoryConfig, "system.category.video_category_id")->getValue() == $params["categoryId"]) {
                $this->renderScript("news/media-list.phtml");
            } else {
                $this->renderScript("news/entry-of-category-list.phtml");
            }
        }
    }

    public function listNewsAction() {
        $params = $this->getRequest()->getParams();
        $this->listEntry($params);
        $filter = array("keyPrefix" => "system.category");
        $categoryConfig = Services::createConfigurationService()->find($filter);

        if (isset($params["categoryId"])) {
            if (ControllerUtils::getParamByKey($categoryConfig, "system.category.video_category_id")->getValue() == $params["categoryId"]) {
                $this->renderScript("news/media-list.phtml");
            } else {
                $this->renderScript("news/entry-of-category-list.phtml");
            }
        }
    }

    private function listEntry($params) {
        $category = array();
        $category[] = Services::createCategoryService()->get($params["categoryId"]);
        //entries
        $filter = $this->buildFilter($params);
        $entries = Services::createEntryService()->find($filter);

        $filter ['metric'] = "record-count";
        $pageCount = Services::createEntryService()->find($filter);

        //paginator        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
        $paginator = Zend_Paginator::factory(range(1, $pageCount));
        $paginator->setCurrentPageNumber($this->getPageNumber());
        $paginator->setItemCountPerPage($filter['pageSize']);
        $this->view->paginator = $paginator;

        //Get Navigation

        $navigations = Services::createConfigurationService()->getCategoryTreeForNavigation(intval($params["categoryId"]), SA_Entity_Menu::LINK_TYPE_ONE_CATEGORY);
        $this->view->navigations = $navigations;


        //Map data
        $this->view->entries = ControllerUtils::mapEntrysHaveCategory($entries, $category);
        if (count($category) > 0) {
            $this->view->category = MapperUtil::mapObject($category[0]);
            // get rewrite url of category by menu
            $menuFilter = array("pageSize" => 0,
                "pageId" => 0,
                "linkId" => $category[0]->getId());
            $menus = Services::createConfigurationService()->findMenus($menuFilter);
            if (count($menus) > 0) {
                $this->view->category["rewriteUrl"] = $menus[0]->getRewriteUrl();
            }
        }
    }

    public function viewArticleAction() {
        $params = $this->getRequest()->getParams();

        // phân tích url
        $rewriteUrl = $params["rewriteUrl"];

        $menu = Services::createConfigurationService()->getMenuByRewriteUrl($rewriteUrl);

        if ($menu != NULL) {
            if ($menu != NULL && $menu->getParentId() != null && $menu->getParentId() > 0) {
                $this->view->selectedMenuId = $menu->getParentId();
            } else {
                $this->view->selectedMenuId = $menu->getId();
            }

            $linkId = $menu->getLinkId();
            $linkType = $menu->getLinkType();
            $params["categoryId"] = $linkId;
            if ($linkType == SA_Entity_Menu::LINK_TYPE_ONE_CATEGORY) { // Nhóm một loại bài viết
                $this->listOneCategory($params);
            } else if ($linkType == SA_Entity_Menu::LINK_TYPE_CATEGORIES) { // Nhóm bài viết
                $this->listCategories($params);
            } else if ($linkType == SA_Entity_Menu::LINK_TYPE_ENTRY) { //  Một bài viết
                $this->viewEntry($rewriteUrl);
                $this->renderScript("news/entry-view.phtml");
            }
        } else {
            $this->viewEntry($rewriteUrl);
            $this->renderScript("news/entry-view.phtml");
        }
    }

    private function buildFilter($params) {
        $filter = array();
        $filter['pageSize'] = Services::createConfigurationService()->get("news.home.page.size")->getValue();

        $filter["pageId"] = 0;
        if (isset($params["pageId"])) {
            $filter["pageId"] = intval($params["pageId"]) - 1;
        }
        if (isset($params["keyword"]) && $params["keyword"] != "") {
            $filter["keyword"] = $params["keyword"];
            $this->view->keyword = $filter["keyword"];
        }
        if (isset($params["categoryId"]) && $params["categoryId"] != -1) {
            $this->view->categoryId = $filter["categoryId"];
            $category = Services::createCategoryService()->get($params['categoryId']);
            if ($category->getType() == SA_Entity_Category::TYPE_MAIN) {
                $filter['parentCategoryId'] = $params['categoryId'];
            } else {
                $filter['categoryId'] = $params['categoryId'];
            }
        }

        if (isset($params["categoryType"]) && $params["categoryType"] != "") {
            $filter["categoryType"] = $params["categoryType"];
            $this->view->categoryType = $filter["categoryType"];
        }
        return $filter;
    }

    public function searchListAction() {
        $params = $this->getRequest()->getParams();

        if (isset($params["t"]) && $params["t"] == "qa") {
            $filter = array("pageId" => 0);
            if (isset($params["pageId"])) {
                $filter["pageId"] = intval($params["pageId"]) - 1;
            }

            $menu = Services::createConfigurationService()->getMenuByRewriteUrl("hoi-dap");

            if ($menu->getParentId() != null && $menu->getParentId() > 0) {
                $this->view->selectedMenuId = $menu->getParentId();
            } else {
                $this->view->selectedMenuId = $menu->getId();
            }

            $filter["pageSize"] = Services::createConfigurationService()->get('qa.home.page.size')->getValue();
            $filter["type"] = array(SA_Entity_Category::TYPE_QA);
            $filter["display"] = 1;
            $filter["orders"] = array(array("column" => "is_top", "type" => "desc"));
            if (isset($params["keyword"]) && $params["keyword"] != "") {
                $filter["keyword"] = $params["keyword"];
                $this->view->keyword = $filter["keyword"];
            } else {
                $this->view->keyword = "";
            }

            $qas = Services::createEntryService()->find($filter);
            $filter['metric'] = "record-count";
            $pageCount = Services::createEntryService()->find($filter);
            $this->view->resultCount = $pageCount;

            //paginator        
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
            $paginator = Zend_Paginator::factory(range(1, $pageCount));
            $paginator->setCurrentPageNumber($this->getPageNumber());
            $paginator->setItemCountPerPage($filter['pageSize']);
            $this->view->paginator = $paginator;

            //Get Navigation
            $navigations = Services::createConfigurationService()->getCategoryTreeForNavigation(0, SA_Entity_Menu::LINK_TYPE_QA);
            $this->view->navigations = $navigations;

            // get supporter
            $this->getSupporter();
            // get inner menu
            $this->getInnerMenu();

            //Map data
            $qasMap = array();
            foreach ($qas as $qa) {
                $qasMap[] = MapperUtil::mapObject($qa, null, Util::$DATE_FORMAT);
            }

            $this->view->qas = $qasMap;

            $this->renderScript("qa/qa-list.phtml");
        } else {
            //entries
            $filter = $this->buildFilter($params);
            $filter["orders"] = "category_id,order_number";
            $filter["type"] = array(SA_Entity_Category::TYPE_CHILD, SA_Entity_Category::TYPE_MAIN);

            if (isset($params["keyword"]) && $params["keyword"] == "") {
                $this->view->keyword = "";
            }
            $entries = Services::createEntryService()->find($filter);
            $conditions = array("pageSize" => 0,
                "pageId" => 0,
                "order" => array("order_number", "asc"));
            $categorys = Services::createCategoryService()->find($conditions);

            $this->getSupporter();
            // get inner menu
            $this->getInnerMenu();

            $filter ['metric'] = "record-count";
            $pageCount = Services::createEntryService()->find($filter);
            $this->view->resultCount = $pageCount;

            $this->view->selectedMenuId = 0;

            //paginator        
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
            $paginator = Zend_Paginator::factory(range(1, $pageCount));
            $paginator->setCurrentPageNumber($this->getPageNumber());
            $paginator->setItemCountPerPage($filter['pageSize']);
            $this->view->paginator = $paginator;

            //Get Navigation
            $navigations = Services::createConfigurationService()->getCategoryTreeForNavigation(intval($params["categoryId"]), SA_Entity_Menu::LINK_TYPE_ENTRY);
            $this->view->navigations = $navigations;

            //Map data
            $entriesMap = ControllerUtils::mapEntrysHaveCategory($entries, $categorys);

            $this->view->entries = $entriesMap;

            $this->renderScript("news/search-list.phtml");
        }
    }

    /**
     * Lấy danh sách nhóm bài viết + bài viết
     * 
     */
    public function listCategories($params) {
        ControllerUtils::prepareMenuData($this->view);
        $categoryService = Services::createCategoryService();

        $parentCategoryId = 0;

        if (isset($params["categoryId"])) {
            $parentCategoryId = $params["categoryId"];
            $this->view->category= MapperUtil::mapObject($categoryService->get($parentCategoryId));
        }

        try {
            //Subcategory
            $conditions = array("pageSize" => 0,
                "pageId" => 0,
                "type" => SA_Entity_Category::TYPE_CHILD,
                "order" => array("order_number", "asc"),
                "parentId" => $parentCategoryId);
            $subCateList = $categoryService->find($conditions);

            //Entry of main category
            $entryPage = Services::createConfigurationService()->get("home.entry.extra.page_size");
            if ($entryPage == null || !is_numeric($entryPage->getValue())) {
                $entryPagesize = 6;
            } else {
                $entryPagesize = $entryPage->getValue() + 1;
            }


            $filter["pageSize"] = $entryPagesize;

            // get list of news by categoryId
            foreach ($subCateList as $cat) {
                $filter["pageId"] = 0;
                $filter["categoryId"] = $cat->getId();
                $filter["type"] = array(SA_Entity_Category::TYPE_CHILD);
                $entryofCats = Services::createEntryService()->find($filter);

                $category['entries'] = ControllerUtils::mapEntrysHaveCategory($entryofCats, $subCateList);
                $category['name'] = $cat->getName();
                $category['id'] = $cat->getId();
                // get rewrite url of category by menu
                $menuFilter = array("pageSize" => 0,
                    "pageId" => 0,
                    "linkId" => $cat->getId());
                $menus = Services::createConfigurationService()->findMenus($menuFilter);
                if (count($menus) > 0) {
                    $category["rewriteUrl"] = $menus[0]->getRewriteUrl();
                }
//                $subList = $this->findSub($main->getId(), $subCateList);
//                $parsedMain['subCates'] = MapperUtil::mapObjects($subList, NULL, Util::$DATE_FORMAT);
                $catList[] = $category;
            }

            // get supporter
            $this->getSupporter();
            // get inner menu
            $this->getInnerMenu();

            //Get Navigation
            $navigations = Services::createConfigurationService()->getCategoryTreeForNavigation(intval($params["categoryId"]), SA_Entity_Menu::LINK_TYPE_CATEGORIES);
            $this->view->navigations = $navigations;


            $this->view->categories = $catList;

            $this->renderScript("news/category-list.phtml");
        } catch (Exception $ex) {
            $this->renderScript("news/category-list.phtml");
        }
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

}
