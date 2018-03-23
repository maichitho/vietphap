
<?php

class System_CategoryController extends Zend_Controller_Action {

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
        $this->view->type = "list";

        $filter = array("pageSize" => 0,
            "pageId" => 0);

        if (!isset($params["type"]))
            $params["type"] = SA_Entity_Category::TYPE_CHILD;

        $filter["type"] = $params["type"];
        $filter["orders"] = array(array('column' => 'order_number', 'type' => 'asc'));
        $list = Services::createCategoryService()->find($filter);

        if ($params["type"] == SA_Entity_Category::TYPE_CHILD) {
            //Get Main Category for Type = PRODUCT
            $filter = array("pageSize" => 0,
                "pageId" => 0,
                "type" => SA_Entity_Category::TYPE_MAIN,
                "orders" => array(array('column' => 'order_number', 'type' => 'asc')));
            $this->view->mainCate = ControllerUtils::mapCategoriesInSystem(Services::createCategoryService()->find($filter), $params["type"]);
        }

        $this->view->category = array();
        $this->view->catType = $params["type"];

        if ($params["type"] == SA_Entity_Category::TYPE_SUPPORT) {
            $this->view->categories = (isset($list)) ? ControllerUtils::mapSupports($list) : array();
        } else {
            $this->view->categories = (isset($list)) ? ControllerUtils::mapCategoriesInSystem($list, $params["type"]) : array();
        }


        $this->renderScript("category/category-list.phtml");
    }

    public function updateAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $categoryService = Services::createCategoryService();

        $params = $this->getRequest()->getParams();

        $id = $params["id"];
        if (strlen($id) <= 0) {
            $this->createAction();
        }
        $categoryEntity = $categoryService->get($id);

        if (isset($categoryEntity) && count($categoryEntity) > 0) {
            $this->view->type = "update";
            if (!$this->getRequest()->isPost()) {
                $this->view->category = ControllerUtils::mapGeneralCategory($categoryEntity);
                $this->view->catType = $params["type"];

                if ($params["type"] == SA_Entity_Category::TYPE_CHILD) {
                    //Get Main Category for Type = PRODUCT
                    $filter = array("pageSize" => 0,
                        "pageId" => 0,
                        "type" => SA_Entity_Category::TYPE_MAIN,
                        "orders" => array(array('column' => 'order_number', 'type' => 'asc')));
                    $this->view->mainCate = ControllerUtils::mapCategoriesInSystem(Services::createCategoryService()->find($filter));
                }

                $this->renderScript("category/category-list.phtml");
                return;
            }

            $categoryEntity = $this->paramToCategory($params, $categoryEntity);
            try {
                $categoryService->update($categoryEntity);
                $this->_redirect("/system/category/list?type=" . $params["type"] . "&status=success");
            } catch (Exception $ex) {
                $this->_redirect("/system/category/update?type=" . $params["type"] . "&status=error");
            }
        }
    }

    public function createAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $paramsUrl = $this->getRequest()->getParams();

        if (isset($paramsUrl["type"])) {
            $this->view->type = "create";
            $this->view->catType = $paramsUrl["type"];

            $categoryService = Services::createCategoryService();

            if (!$this->getRequest()->isPost()) {
                $this->view->category = array();

                if ($paramsUrl["type"] == SA_Entity_Category::TYPE_CHILD) {
                    //Get Main Category for Type = PRODUCT
                    $filter = array("pageSize" => 0,
                        "pageId" => 0,
                        "type" => SA_Entity_Category::TYPE_MAIN,
                        "orders" => array(array('column' => 'order_number', 'type' => 'asc')));
                    $this->view->mainCate = ControllerUtils::mapCategoriesInSystem($categoryService->find($filter));
                }

                $this->renderScript("category/category-list.phtml");
                return;
            }

            $categoryEntity = $this->paramToCategory($paramsUrl, new SA_Entity_Category());
            $categoryEntity->setCreateTime(new DateTime());

            try {
                $mapCategory = $categoryService->create($categoryEntity);
                $this->_redirect("/system/category/list?type=" . $paramsUrl["type"] . "&status=success");
            } catch (Exception $ex) {
                // do smth when error
//                var_dump($ex) ;
//                $this->renderScript("category/category-list.phtml");
                $this->_redirect("/system/category/list?type=" . $paramsUrl["type"] . "&status=error");
            }
        }
    }

    public function upCategoryAction() {
        $params = $this->getRequest()->getParams();
        $categoryService = Services::createCategoryService();
        $id = $params["id"];
        $type = $params["type"];
        try {
            $categoryService->up($id);
            $this->_redirect("/system/category/list?type=" . $type);
        } catch (Exception $ex) {
// do smth when error
        }
    }

    public function downCategoryAction() {
        $params = $this->getRequest()->getParams();
        $categoryService = Services::createCategoryService();
        $id = $params["id"];
        $type = $params["type"];
        try {
            $categoryService->down($id);
            $this->_redirect("/system/category/list?type=" . $type);
        } catch (Exception $ex) {
// do smth when error
        }
    }

    public function deleteCategoryAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $params = $this->getRequest()->getParams();
        $id = $params["id"];
        Services::createCategoryService()->delete($id);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true));
    }

    private function paramToCategory($param, $category) {
        if (isset($param["id"])) {
            $category->setId($param["id"]);
        }
        if (isset($param["name"])) {
            $category->setName($param["name"]);
        }
        if (isset($param["description"])) {
            $category->setDescription($param["description"]);
        }
        
         if (isset($param["seoKeyword"])) {
            $category->setSeoKeyword($param["seoKeyword"]);
        }
        
         if (isset($param["seoDescription"])) {
            $category->setSeoDescription($param["seoDescription"]);
        }
        if (isset($param["orderNumber"])) {
            $category->setOrderNumber($param["orderNumber"]);
        }
        if (isset($param["parentId"])) {
            $category->setParentId($param["parentId"]);
        }
        if (isset($param["imagePath"])) {
            $category->setImagePath($param["imagePath"]);
        }
        if (isset($param["iconPath"])) {
            $category->setIconPath($param["iconPath"]);
        }
        if (isset($param["type"])) {
            $category->setType($param["type"]);

            if ($param["type"] == SA_Entity_Category::TYPE_SUPPORT) {
                $tmp = array("SKYPE_USERNAME" => $param["skype_username"],
                    "YAHOO_USERNAME" => $param["yahoo_username"],
                    "MOBILE" => $param["mobile"]);
                $category->setDescription(json_encode($tmp));
            }
        }
        if (isset($param["parentCateId"])) {
            if ($param["parentCateId"] != $category->getParentId()) {
                //get new Order for category
                $newOrder = intval(Services::createCategoryService()->getMaxOrderByParentId($param["parentCateId"])) + 1;
                $category->setOrderNumber($newOrder);
            }
            $category->setParentId($param["parentCateId"]);
        }

        $category->setUpdateTime(new DateTime());

        return $category;
    }

    public function asyncListCategoriesWithFirstElementAction() {
        $params = $this->getRequest()->getParams();

        switch ($params["type"]) {
            case SA_Entity_Category::TYPE_CATEGORIES : {
                    $categories = ControllerUtils::mapCategories(Services::createCategoryService()->findServiceCategory(array()));
                    $nameFirstElement = Util::translate("system.menu.all_service_categories");
                    break;
                }
            case SA_Entity_Category::TYPE_ONE_CATEGORY : {
                    $categories = ControllerUtils::mapCategories(Services::createCategoryService()->findServiceCategory(array()));
                    $nameFirstElement = Util::translate("system.menu.all_service_categories");
                    break;
                }
            case SA_Entity_Category::TYPE_ENTRY : {
                    $categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => $params["type"])));
                    $nameFirstElement = Util::translate("system.menu.all_entry_categories");
                    break;
                }
            case SA_Entity_Category::TYPE_QA : {
                    $categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => $params["type"])));
                    $nameFirstElement = Util::translate("system.menu.all_qa_categories");
                    break;
                }
            default : {
                    $categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => $params["type"])));
                    $nameFirstElement = '';
                }
        }
        $firstElement = array("id" => "0", "parentId" => 0, "name" => $nameFirstElement);
        array_unshift($categories, $firstElement);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true, "data" => $categories));
    }

    public function asyncListCategoriesAction() {
        $params = $this->getRequest()->getParams();

        $categories = ControllerUtils::mapCategories(Services::createCategoryService()->find(array("type" => $params["type"])));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true, "categories" => $categories));
    }

    /**
     * Location Settings
     */
    public function listLocationAction() {

        $this->view->type = "list";

        // Get all cities
        $filter = array("pageSize" => 0,
            "pageId" => 0,
            "parentId" => 0,
            "orders" => array(array('column' => 'lft', 'type' => 'asc')));
        $list = Services::createCategoryService()->findLocation($filter);
        $this->view->parentLocations = (isset($list)) ? ControllerUtils::mapCategories($list) : array();

        $params = $this->getRequest()->getParams();
        $parentId = (isset($params["selectedParentId"]) && $params["selectedParentId"] != "") ? $params["selectedParentId"] : $this->view->parentLocations[0]["id"];
        $firstElement = array("id" => "0", "parentId" => -1, "name" => Util::translate("form.select.option.all"));
        array_unshift($this->view->parentLocations, $firstElement);

        if ($parentId != "0") {
            $filter = array("pageSize" => 0,
                "pageId" => 0,
                "parentId" => $parentId,
                "orders" => array(array('column' => 'lft', 'type' => 'asc')));
        } else {
            $filter = array("pageSize" => 0,
                "pageId" => 0,
                "orders" => array(array('column' => 'lft', 'type' => 'asc')));
        }

        $list = Services::createCategoryService()->findLocation($filter);
        $this->view->locations = (isset($list)) ? ControllerUtils::mapCategories($list) : array();
        $this->view->selectedParentId = $parentId;

        $this->view->location = array();
        $this->renderScript("category/location-list.phtml");
    }

    public function createLocationAction() {
        $categoryService = Services::createCategoryService();

        $this->view->type = "create";
        $params = $this->getRequest()->getParams();

        if (!$this->getRequest()->isPost()) {
            $this->view->location = array("parentId" => (isset($params['selectedParentId']) && $params['selectedParentId'] != "") ? $params['selectedParentId'] : '0');
            $filter = array("pageSize" => 0,
                "pageId" => 0,
                "parentId" => 0,
                "orders" => array(array('column' => 'lft', 'type' => 'asc')));
            $this->view->mainLocation = ControllerUtils::mapCategories($categoryService->findLocation($filter));
            array_unshift($this->view->mainLocation, array('id' => 0, 'name' => Util::translate('form.label.blank')));
//            $this->view->mainLocation = array();

            $this->renderScript("category/location-list.phtml");
            return;
        }
        $location = $this->paramToLocation($params, new SA_Entity_Location());

        try {
            $categoryService->createLocation($location);
            $this->_redirect("/system/category/list-location?status=success&selectedParentId=" . $location->getParentId());
        } catch (Exception $ex) {
            // do smth when error
            $this->_redirect("/system/category/update-location?status=error");
        }
    }

    public function updateLocationAction() {
        $params = $this->getRequest()->getParams();
        $categoryService = Services::createCategoryService();

        $this->view->type = "update";

        if (!$this->getRequest()->isPost()) {
            if (isset($params["id"])) {
                $id = $params["id"];
                $this->view->location = ControllerUtils::mapGeneralCategory($categoryService->getLocation($id));
            } else {
                $this->view->location = array();
            }

            $filter = array("pageSize" => 0,
                "pageId" => 0,
                "parentId" => 0,
                "orders" => array(array('column' => 'lft', 'type' => 'asc')));
            $this->view->mainLocation = ControllerUtils::mapCategories($categoryService->findLocation($filter));
            array_unshift($this->view->mainLocation, array('id' => 0, 'name' => Util::translate('form.label.blank')));

            $this->renderScript("category/location-list.phtml");
            return;
        }

        $id = $params["id"];
        $location = $categoryService->getLocation($id);

        $this->paramToLocation($params, $location);
        try {
            $categoryService->updateLocation($location);
            $this->_redirect("/system/category/list-location?status=success&selectedParentId=" . $location->getParentId());
//            $this->_redirect("/system/category/update-location?id=" . $id . "&status=success");
        } catch (Exception $ex) {
            $this->_redirect("/system/category/list-location?status=error");
//            $this->_redirect("/system/category/update-location?status=error");
        }
    }

    public function deleteLocationAction() {
        $params = $this->getRequest()->getParams();
        $categoryService = Services::createCategoryService();
        $id = $params["id"];
        $categoryService->deleteLocation($id);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true));
    }

    private function paramToLocation($param, $location) {
        if (isset($param["id"])) {
            $location->setId($param["id"]);
        }
        if (isset($param["name"])) {
            $location->setName($param["name"]);
        }
        if (isset($param["code"])) {
            $location->setCode($param["code"]);
        }
        if (isset($param["description"])) {
            $location->setDescription($param["description"]);
        }
        if (isset($param["parentLocationId"])) {
            $location->setParentId($param["parentLocationId"]);
            $parentLocation = Services::createCategoryService()->getLocation($param["parentLocationId"]);
            if (isset($parentLocation) && count($parentLocation) > 0) {
                $location->setDisplayName(substr($parentLocation->getDisplayName(), 0, strrpos($parentLocation->getDisplayName(), $parentLocation->getName())) . '-- ' . $location->getName());
            } else {
                $location->setDisplayName($location->getName());
            }
        }

        return $location;
    }

    public function upLocationAction() {
        $params = $this->getRequest()->getParams();
        $categoryService = Services::createCategoryService();
        $id = $params["id"];
        $location = $categoryService->getLocation($id);
        try {
            $categoryService->upLocation($id);
            $this->_redirect("/system/category/list-location?selectedParentId=" . $location->getParentId());
        } catch (Exception $ex) {
// do smth when error
        }
    }

    public function downLocationAction() {
        $params = $this->getRequest()->getParams();
        $categoryService = Services::createCategoryService();
        $id = $params["id"];
        $location = $categoryService->getLocation($id);
        try {
            $categoryService->downLocation($id);
            $this->_redirect("/system/category/list-location?selectedParentId=" . $location->getParentId());
        } catch (Exception $ex) {
// do smth when error
        }
    }

}
