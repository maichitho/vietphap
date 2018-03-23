<?php

class Home_QaController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        ControllerUtils::prepareMenuData($this->view);
    }

    public function indexAction() {
        // action body
    }

    public function listAction() {
//        ControllerUtils::prepareMenuData($this->view);
        $params = $this->getRequest()->getParams();
//        //entries

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

        $qas = Services::createEntryService()->find($filter);
        $filter['metric'] = "record-count";
        $pageCount = Services::createEntryService()->find($filter);

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
    }

    public function viewAction() {
        ControllerUtils::prepareMenuData($this->view);
        $params = $this->getRequest()->getParams();
        $entry = Services::createEntryService()->get($params["id"]);
        $suggestionEntries = Services::createEntryService()->find(array("pageSize" => 10, "pageId" => 0, "categoryId" => $entry->getCategoryId()));
        $suggestionEntriesMap = array();
        foreach ($suggestionEntries as $suggestionEntry) {
            if ($entry->getId() == $suggestionEntry->getId()) {
                continue;
            }
            $suggestionEntriesMap[] = MapperUtil::mapObject($suggestionEntry, null, Util::$DATE_FORMAT);
        }
        $this->view->qa = MapperUtil::mapObject($entry, null, Util::$DATE_FORMAT);
        $this->view->suggestionQas = $suggestionEntriesMap;

        //Get Navigation
        $navigations = Services::createConfigurationService()->getCategoryTreeForNavigation($entry->getCategoryId(), SA_Entity_Menu::LINK_TYPE_QA);
        $navigations[] = array("name" => $entry->getTitle(), "link" => "");
        $this->view->navigations = $navigations;

        $this->renderScript("qa/qa-view.phtml");
    }

    public function asyncLoadMoreResultAction() {
        $params = $this->getRequest()->getParams();

        $filter = array("pageId" => (isset($params['pageId']) && $params["pageId"] != "") ? $params["pageId"] : 0,
            "pageSize" => Services::createConfigurationService()->get('qa.home.page.size')->getValue(),
            "type" => array(SA_Entity_Category::TYPE_QA),
            "display" => "1",
            "orders" => array(array("column" => "is_top", "type" => "desc")));

        $qas = Services::createEntryService()->find($filter);
        $filter ['metric'] = "record-count";
        $pageCount = Services::createEntryService()->find($filter);

        //Map data
        $qasMap = array();
        foreach ($qas as $qa) {
            $qasMap[] = MapperUtil::mapObject($qa, null, Util::$DATE_FORMAT);
        }

        $showMoreLink = ($pageCount > ((intval($filter["pageId"]) + 1) * intval($filter["pageSize"]))) ? '1' : '0';

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("success" => true, "showMoreLink" => $showMoreLink,
            "items" => $qasMap));
    }

    public function asyncInsertQaAction() {
        $params = $this->getRequest()->getParams();

        $conditions = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Category::TYPE_QA,
            "order" => array("order_number", "asc"));
        $subCateList = Services::createCategoryService()->find($conditions);
        if (count($subCateList) > 0) {
            $categoryId = $subCateList[0]->getId();
        }

        $qa = new SA_Entity_Entry();
        $qa->setTitle($params["title"])
                ->setDescription($params["description"])
                ->setAsker($params["asker"])
                ->setAskerEmail($params["askerEmail"])
                ->setCategoryId($categoryId)
                ->setUpdateTime(new DateTime())
                ->setCreateTime(new DateTime())
                ->setDisplay(0)
                ->setTotalViews(1)
                ->setIsTop(1)
                ->setCreatorId(0);

        Services::createEntryService()->create($qa);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("success" => true));
    }

    private function buildFilter($params) {
        $filter = array();
        $filter['pageSize'] = Services::createConfigurationService()->get("news.home.page.size")->getValue();
        $filter["pageId"] = 0;
        $filter["categoryType"] = SA_Entity_Category::TYPE_QA;
        $this->view->categoryType = $filter["categoryType"];

        if (isset($params["pageId"])) {
            $filter["pageId"] = intval($params["pageId"]) - 1;
        }
        if (isset($params["keyword"]) && $params["keyword"] != "") {
            $filter["keyword"] = $params["keyword"];
            $this->view->keyword = $filter["keyword"];
        }
        if (isset($params["categoryId"]) && $params["categoryId"] != -1) {
            $filter["categoryId"] = $params["categoryId"];
            $this->view->categoryId = $filter["categoryId"];
        }

        return $filter;
    }

    private function buildFilterForAgency($params) {

        if (isset($params['address']) && $params['address'] != "") {
            $this->view->address = $params['address'];
            $filter['address'] = $params['address'];
        }

        if (isset($params['keyword']) && $params['keyword'] != "") {
            $this->view->keyword = $params['keyword'];
            $filter['name'] = $params['keyword'];
        }
        if (isset($params['cityId']) && $params['cityId'] != "") {
            $this->view->cityId = $params['cityId'];
            $filter['cityId'] = $params['cityId'];

            if (isset($params['districtId']) && $params['districtId'] != "" && $params['districtId'] != "0") {
                $this->view->districtId = $params['districtId'];
                $filter['districtId'] = $params['districtId'];
            }
        }
        return $filter;
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
