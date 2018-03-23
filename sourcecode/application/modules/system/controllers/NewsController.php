<?php

class System_NewsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function listAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        $filter = $this->getFilterForListEntry($params);

        $entries = ControllerUtils::mapEntries(Services::createEntryService()->find($filter));
        $filter ['metric'] = "record-count";
        $pageCount = Services::createEntryService()->find($filter);
        $this->view->entries = isset($entries) ? $entries : array();

        //paginator        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
        $paginator = Zend_Paginator::factory(range(1, $pageCount));
        $paginator->setCurrentPageNumber($this->getPageNumber());
        $paginator->setItemCountPerPage($filter['pageSize']);
        $this->view->paginator = $paginator;

        //Set search conditions
        $this->view->keyword = isset($this->view->keyword) ? $this->view->keyword : '';
        $this->view->categoryId = isset($this->view->categoryId) ? $this->view->categoryId : '0';
        $this->view->createTimeFrom = isset($this->view->createTimeFrom) ? $this->view->createTimeFrom : '';
        $this->view->createTimeTo = isset($this->view->createTimeTo) ? $this->view->createTimeTo : '';

        //get data for selections
        $this->view->categories = ControllerUtils::mapCategories(Services::createCategoryService()->findServiceCategory(array("type" => SA_Entity_Category::TYPE_SERVICE)));
        $firstElement = array("id" => "0", "parentId" => 0, "name" => Util::translate("form.select.option.all"));
        array_unshift($this->view->categories, $firstElement);

        //render        
        $this->renderScript("news/news-list.phtml");
    }

    public function createAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();
            //var_dump($this->buildEntry(new SA_Entity_Entry(), $params));
            $entryId = Services::createEntryService()->create($this->buildEntry(new SA_Entity_Entry(), $params));

            // add related $entry
            if (isset($params['entryIds']) && $params['entryIds'] != "") {
                $entryIds = explode(";", $params['entryIds']);
                if (count($entryIds) > 0) {
                    foreach ($entryIds as $relatedId) {
                        Services::createEntryService()->addRelation($entryId, $relatedId);
                    }
                }
            }

            $this->view->result = true;
            $this->_redirect("/system/news/list");
        }

        $entry = new SA_Entity_Entry();
        $entry->setIsTop('1');
        $entry->setDisplay('1');
        $entry->setOrderNumber(intval(Services::createEntryService()->getMaxOrderNumber()) + 1);

        // get selection entries for choosing related entry
        $selFilter = array("pageSize" => 20,
            "pageId" => 0);
        $this->view->selectiveEntries = ControllerUtils::mapEntries(Services::createEntryService()->find($selFilter));
        $this->view->entry = ControllerUtils::mapEntry($entry);
        $this->view->relatedEntries = array();

        $this->view->categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => SA_Entity_Category::TYPE_SERVICE)));
        $this->view->type = "create";
        $this->view->entryId = '';
        $this->renderScript("news/news-cnu.phtml");
    }

    public function asyncListAction() {
        $params = $this->getRequest()->getParams();

        $filter = array("pageSize" => 0,
            "pageId" => 0);

        if (isset($params['keyword'])) {
            $filter["keyword"] = $params['keyword'];
        } else {
            $filter["keyword"] = "";
        }

        // get entries
        $entries = ControllerUtils::mapEntries(Services::createEntryService()->find($filter));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("items" => $entries));
    }

    public function updateAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $params = $this->getRequest()->getParams();

        if (isset($params["id"])) {
            $entry = Services::createEntryService()->get($params["id"]);
        }
        if (isset($params["type"])) {
            $type = $params["type"];
            $this->view->typeUpdate = $type;
        }

        $user = Services::createAuthenticationService()->getUser();
        $creator = Services::createUserService()->get($entry->getCreatorId());

        if (($user->getType() == SA_Entity_User::TYPE_EDITOR && $user->getId() != $entry->getCreatorId()) || ($user->getType() == SA_Entity_User::TYPE_MANAGER && $creator->getType() == SA_Entity_User::TYPE_ADMINISTRATOR)) {
            $this->listAction();
            return;
        }

        if ($this->_request->isPost()) {
            $entry = $this->buildEntry($entry, $params);
            $entryId = 0;
//            if not exist
            if ($entry->getId() > 0 && !isset($params["type"])) {
                Services::createEntryService()->update($entry);
                $entryId = $entry->getId();
            } else {
                $entryId = Services::createEntryService()->create($entry);
            }

            // update related entry
            Services::createEntryService()->removeAllRelation($entryId);
            // add related $entry
            if (isset($params['entryIds']) && $params['entryIds'] != "") {
                $entryIds = explode(";", $params['entryIds']);
                if (count($entryIds) > 0) {
                    foreach ($entryIds as $relatedId) {
                        Services::createEntryService()->addRelation($entryId, $relatedId);
                    }
                }
            }

            $this->view->result = true;
            $this->_redirect("/system/news/list");
        }

        // get related product
        $filterRelated = array("pageSize" => 0,
            "pageId" => 0,
            "relateOfEntryId" => $params['id']);
        $this->view->relatedEntries = MapperUtil::mapObjects(Services::createEntryService()->find($filterRelated));

        // get selection entries for choosing related entry
        $selFilter = array("pageSize" => 20,
            "pageId" => 0);
        $this->view->selectiveEntries = ControllerUtils::mapEntries(Services::createEntryService()->find($selFilter));

        $this->view->entry = ControllerUtils::mapEntry(Services::createEntryService()->get($params["id"]));
        $this->view->categories = ControllerUtils::mapProductCategories(Services::createCategoryService()->findServiceCategory(array("type" => SA_Entity_Category::TYPE_SERVICE)));
        $this->view->type = "update";
        $this->view->entryId = $params["id"];
        $this->renderScript("news/news-cnu.phtml");
    }

    public function deleteAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        Services::createEntryService()->delete($params["id"]);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true));
    }

    public function listQaAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        $params['pageSize'] = Services::createConfigurationService()->get("news.system.page.size")->getValue();

        if (!isset($params['pageId'])) {
            $params['pageId'] = 0;
        } else {
            $params['pageId'] --;
        }

        if (!isset($params['keyword']) || strlen($params['keyword']) <= 0) {
            unset($params['keyword']);
        } else {
            $this->view->keyword = $params['keyword'];
        }

        if (!isset($params['asker']) || strlen($params['asker']) <= 0) {
            unset($params['asker']);
        } else {
            $this->view->asker = $params['asker'];
        }

        if (!isset($params['askerEmail']) || strlen($params['askerEmail']) <= 0) {
            unset($params['askerEmail']);
        } else {
            $this->view->askerEmail = $params['askerEmail'];
        }

        $params['type'] = array(SA_Entity_Category::TYPE_QA);

        $qas = ControllerUtils::mapEntries(Services::createEntryService()->find($params));
        $params ['metric'] = "record-count";
        $pageCount = Services::createEntryService()->find($params);
        $this->view->qas = isset($qas) ? $qas : array();

        //paginator        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
        $paginator = Zend_Paginator::factory(range(1, $pageCount));
        $paginator->setCurrentPageNumber($this->getPageNumber());
        $paginator->setItemCountPerPage($params['pageSize']);
        $this->view->paginator = $paginator;

        //Set search conditions
        $this->view->keyword = isset($this->view->keyword) ? $this->view->keyword : '';
        $this->view->asker = isset($this->view->asker) ? $this->view->asker : '';
        $this->view->askerEmail = isset($this->view->askerEmail) ? $this->view->askerEmail : '';

        //render
        $this->view->categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => SA_Entity_Category::TYPE_QA,
                            'orders' => array(array('column' => 'order_number', 'type' => 'asc')))));
        $this->renderScript("news/qa-list.phtml");
    }

    public function createQaAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();
            Services::createEntryService()->create($this->buildEntry(new SA_Entity_Entry(), $params));
            $this->view->result = true;
            $this->_redirect("/system/news/list-qa");
        }
        $entry = new SA_Entity_Entry();
        $entry->setIsTop('1');

        $this->view->entry = ControllerUtils::mapEntry($entry);
        $this->view->categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => SA_Entity_Category::TYPE_QA,
                            'orders' => array(array('column' => 'order_number', 'type' => 'asc')))));
        $this->view->type = "create";
        $this->view->entryId = '';
        $this->renderScript("news/qa-cnu.phtml");
    }

    public function updateQaAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        $entry = Services::createEntryService()->get($params["id"]);

        if ($this->_request->isPost()) {
            $entry = $this->buildEntry($entry, $params);

//            if not exist
            if ($entry->getId() > 0) {
                Services::createEntryService()->update($entry, true);
            } else {
                $entry->setId($params["id"]);
                Services::createEntryService()->create($entry);
            }
            $this->view->result = true;
            $this->_redirect("/system/news/list-qa");
        }

        $this->view->entry = ControllerUtils::mapEntry(Services::createEntryService()->get($params["id"]));
        $this->view->categories = ControllerUtils::mapProductCategories(Services::createCategoryService()->find(array("type" => SA_Entity_Category::TYPE_QA,
                            'orders' => array(array('column' => 'order_number', 'type' => 'asc')))));
        $this->view->type = "update";
        $this->view->entryId = $params["id"];

        $this->renderScript("news/qa-cnu.phtml");
    }

    public function asyncCheckRewriteUrlAction() {
        $retVal = FALSE;
        $params = $this->getRequest()->getParams();
        $entry = Services::createEntryService()->getByUrl($params["rewriteUrl"]);
        if ($entry != NULL) {
            $retVal = TRUE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $retVal));
    }

    public function asyncUpdateDisplayStatusAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        $result = false;

        if (isset($params['id']) && $params['id'] != "") {
            $id = $params['id'];
            $display = $params['display'];

            $entry = Services::createEntryService()->get($id);
            $entry->setDisplay($display);
            Services::createEntryService()->update($entry);
            $result = true;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("success" => $result));
    }

    private function getFilterForListEntry($params) {
        $filter['pageSize'] = Services::createConfigurationService()->get("news.system.page.size")->getValue();
        $categoryService = Services::createCategoryService();

        if (!isset($params['pageId'])) {
            $filter['pageId'] = 0;
        } else {
            $filter['pageId'] = intval($params['pageId']) - 1;
        }

        if (!isset($params['type']) || $params['type'] == "") {
            $this->view->type = "";
//            $filter['type'] = array(SA_Entity_Category::TYPE_MAIN, SA_Entity_Category::TYPE_CHILD, SA_Entity_Category::TYPE_ENTRY);
            $this->view->categories = array();
        } else {
            $this->view->type = $params['type'];
            if ($params['type'] == SA_Entity_Category::TYPE_ENTRY) {
                $filter['type'] = array(SA_Entity_Category::TYPE_ENTRY);
                $this->view->categories = ControllerUtils::mapCategories($categoryService->find(array("type" => SA_Entity_Category::TYPE_ENTRY)));
            } else {
                $filter['type'] = array(SA_Entity_Category::TYPE_MAIN, SA_Entity_Category::TYPE_CHILD);
                $this->view->categories = ControllerUtils::mapCategories($categoryService->find(array("type" => SA_Entity_Category::TYPE_SERVICE)));
            }
        }

        if (isset($params['createTimeFrom']) && $params['createTimeFrom'] != "") {
            $this->view->createTimeFrom = $params['createTimeFrom'];
            $filter['createTimeFrom'] = Util::toSQLDateTimeStringWithFromTo($params['createTimeFrom'], Util::FROM_TIME_TYPE);
        }

        if (isset($params['createTimeTo']) && $params['createTimeTo'] != "") {
            $this->view->createTimeTo = $params['createTimeTo'];
            $filter['createTimeTo'] = Util::toSQLDateTimeStringWithFromTo($params['createTimeTo'], Util::TO_TIME_TYPE);
        }

        if (isset($params['keyword']) && $params['keyword'] != "") {
            $this->view->keyword = $params['keyword'];
            $filter['title'] = $params['keyword'];
        }

        if (isset($params['categoryId']) && $params['categoryId'] != "0") {
            $this->view->categoryId = $params['categoryId'];
            $category = $categoryService->get($params['categoryId']);
            if ($category->getType() == SA_Entity_Category::TYPE_MAIN) {
                $filter['parentCategoryId'] = $params['categoryId'];
            } else {

                $filter['categoryId'] = $params['categoryId'];
            }
        }
        return $filter;
    }

    private function buildEntry(SA_Entity_Entry $entry, $params) {

        $conditions = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Category::TYPE_QA,
            "order" => array("order_number", "asc"));
        $subCateList = Services::createCategoryService()->find($conditions);
        if (count($subCateList) > 0) {
            $categoryId = $subCateList[0]->getId();
        }

        if (isset($params["categoryId"])) {
            $entry->setCategoryId($params["categoryId"]);
        } else {
            $entry->setCategoryId($categoryId);
        }
        if (isset($params["content"])) {
            $entry->setContent(stripslashes($params["content"]));
        }
        if (isset($params["summary"])) {
            $entry->setDescription($params["summary"]);
        }
        if (isset($params["title"])) {
            $entry->setTitle($params["title"]);
        }

        if (isset($params["isTop"])) {
            $entry->setIsTop($params["isTop"]);
        }
        if (isset($params["tags"])) {
            $entry->setTags($params["tags"]);
        }

        if (isset($params["imagePath"])) {
            $url = ($params["imagePath"] != "") ? $params["imagePath"] : Services::createConfigurationService()->get('icon.no.image.url')->getValue();
            $entry->setImagePath($url);
            $fileInfo = pathinfo($url);
            $entry->setThumbnailUrl(($params["imagePath"] != "") ? ($fileInfo['dirname'] . '/thumbnail/' . $fileInfo["filename"] . "." . $fileInfo["extension"]) : Services::createConfigurationService()->get('icon.no.image.url')->getValue());
        }

        if (isset($params["display"])) {
            $entry->setDisplay($params["display"]);
        }
        if (isset($params["orderNumber"])) {
            $entry->setOrderNumber(($params["orderNumber"] . length > 0) ? $params["orderNumber"] : (intval(Services::createEntryService()->getMaxOrderNumber()) + 1));
        } else {
            $entry->setOrderNumber("1000");
        }
        if (isset($params["seoTitle"])) {
            $entry->setSeoTitle($params["seoTitle"]);
        }
        if (isset($params["seoKeyword"])) {
            $entry->setSeoKeyword($params["seoKeyword"]);
        }
        if (isset($params["seoDescription"])) {
            $entry->setSeoDescription($params["seoDescription"]);
        }
        if (isset($params["asker"])) {
            $entry->setAsker($params["asker"]);
        }
        if (isset($params["askerEmail"])) {
            $entry->setAskerEmail($params["askerEmail"]);
        }
        if (isset($params["rewriteUrl"])) {
            $entry->setRewriteUrl($params["rewriteUrl"]);
        }
        $entry->setUpdateTime(new DateTime());

        if ($entry->getId() > 0) {
            
        } else {
            $entry->setCreatorId(Services::createAuthenticationService()->getUser()->getId())
                    ->setCreateTime(new DateTime());
        }
        return $entry;
    }

}
