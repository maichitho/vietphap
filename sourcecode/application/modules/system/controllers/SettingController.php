<?php

class System_SettingController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->params = $this->getRequest()->getParams();
//        $this->languages = json_decode(Services::createConfigurationService()->getParam("languages")->getValue(), true);
//        if (!isset($this->params['languageCode'])) {
//            $this->params['languageCode'] = $this->languages[0]['code'];
//        }
    }

    public function indexAction() {
        // action body
    }

    public function listParamAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $params = Services::createConfigurationService()->find(array());
        $this->view->generalParams = Services::createConfigurationService()->find(array("paramType" => SA_Entity_Param::PARAM_TYPE_GENERAL, "orders" => array(array("column" => "order_number", "type" => "asc", "key2" => "desc"))));
        $this->view->imageParams = Services::createConfigurationService()->find(array("paramType" => SA_Entity_Param::PARAM_TYPE_IMAGE, "orders" => array(array("column" => "order_number", "type" => "asc", "key2" => "desc"))));
        $this->view->seoParams = Services::createConfigurationService()->find(array("paramType" => SA_Entity_Param::PARAM_TYPE_SEO, "orders" => array(array("column" => "order_number", "type" => "asc", "key2" => "desc"))));
        $this->view->snetworkParams = Services::createConfigurationService()->find(array("paramType" => SA_Entity_Param::PARAM_TYPE_SNETWORK, "orders" => array(array("column" => "order_number", "type" => "asc", "key2" => "desc"))));
        $this->renderScript("setting/param-list.phtml");
    }

    public function listMenuAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $types = array();
        $types[] = array('code' => SA_Entity_Menu::TYPE_HEADER, 'name' => Util::translate('system.menu_type.header'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_FOOTER, 'name' => Util::translate('system.menu_type.footer'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_LEFT, 'name' => Util::translate('system.menu_type.left'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_RIGHT, 'name' => Util::translate('system.menu_type.right'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_INNER_RIGHT, 'name' => Util::translate('system.menu_type.innerRight'));
        $this->view->types = $types;

        $params = $this->getRequest()->getParams();
        $this->view->type = (isset($params["type"]) && $params["type"] != "") ? $params["type"] : SA_Entity_Menu::TYPE_HEADER;

        $params['pageSize'] = 0;
        $params['pageId'] = 0;
        $params['type'] = $this->view->type;
        $menus = Services::createConfigurationService()->findMenus($params);
        $this->view->menus = $menus;

        $this->renderScript("setting/menu-list.phtml");
    }

    public function asyncUpdateParamAction() {
        $param = Services::createConfigurationService()->get($this->params["key"]);
        $param->setUpdateTime(new DateTime());
        $param->setValue(stripslashes($this->params["value"]));
        Services::createConfigurationService()->update($param);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true));
    }

    public function createMenuAction() {
        $msg = ControllerUtils::validatePermision($this->view);
        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        if ($this->_request->isPost()) {
            $menu = new SA_Entity_Menu();
            $this->buildMenu($menu, $params);
            Services::createConfigurationService()->createMenu($menu);
            $this->redirect("/system/setting/list-menu?type=" . $menu->getType());
        }
        $types = array();
        $types[] = array('code' => SA_Entity_Menu::TYPE_HEADER, 'name' => Util::translate('system.menu_type.header'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_FOOTER, 'name' => Util::translate('system.menu_type.footer'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_LEFT, 'name' => Util::translate('system.menu_type.left'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_RIGHT, 'name' => Util::translate('system.menu_type.right'));
        $types[] = array('code' => SA_Entity_Menu::TYPE_INNER_RIGHT, 'name' => Util::translate('system.menu_type.innerRight'));
        $this->view->types = $types;

        $selectedType = (isset($params["type"]) && $params["type"] != "") ? $params["type"] : $types[0]['code'];
        $selectedMenuName = ($selectedType == SA_Entity_Menu::TYPE_HEADER ) ? Util::translate('system.menu_type.header') :
                (($selectedType == SA_Entity_Menu::TYPE_FOOTER ) ? Util::translate('system.menu_type.footer') :
                        (($selectedType == SA_Entity_Menu::TYPE_LEFT ) ? Util::translate('system.menu_type.left') :
                                (($selectedType == SA_Entity_Menu::TYPE_RIGHT ) ? Util::translate('system.menu_type.right') : Util::translate('system.menu_type.innerRight'))));
        $parentMenus = Services::createConfigurationService()->findMenus(array("pageId" => 0, "pageSize" => 0, "parentId" => 0,
            'type' => $selectedType));
        $parentMenus = (isset($parentMenus) && count($parentMenus) > 0) ? ControllerUtils::mapMenus($parentMenus) : array();
        array_unshift($parentMenus, array('id' => 0, 'name' => ''));
        $this->view->parentMenus = $parentMenus;
        $this->view->treeMenus = ControllerUtils::mapMenus($this->sortMenus(Services::createConfigurationService()->findMenus(array('type' => $selectedType))));
        $this->view->treeName = $selectedMenuName;

        $menu = Services::createConfigurationService()->getMenu("1", NULL);
        $this->view->menu = array("status" => "1", "type" => $selectedType);
        $this->view->type = "create";
        $this->renderScript("setting/menu-cnu.phtml");
    }

    private function buildMenu($menu, $params) {
        /* @var $menu SA_Entity_Menu */
        $menu->setCode("")
                ->setType($params["type"])
                ->setDescription($params["description"])
                ->setLinkId($params["linkId"])
                ->setLinkType($params["linkType"])
                ->setLinkUrl($params["linkUrl"])
                ->setName($params["name"])
                ->setLogoPath($params['logoPath'])
                ->setParentId($params["parentId"])
                ->setHtmlCode($params["htmlCode"])
                ->setStatus($params["status"])
                ->setUpdateTime(new DateTime())
                 ->setRewriteUrl($params["rewriteUrl"])
                ->setImagePath($params["imagePath"]);
        if ($menu->getId() > 0) {
            
        } else {
            $menu->setCreateTime(new DateTime());
        }

        if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_MANUAL) {
            $menu->setHtmlCode('')
                    ->setLinkId(0)
                    ->setImagePath('');
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_IMAGE) {
            $menu->setLinkId(0)
                    ->setHtmlCode('');
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_HTML) {
            $menu->setLinkUrl('')
                    ->setLinkId(0)
                    ->setImagePath('');
        } else {
            $menu->setHtmlCode('')
                    ->setLinkUrl('')
                    ->setImagePath('');
        }
    }

    public function updateMenuAction() {
        $menu = Services::createConfigurationService()->getMenu($this->params["id"], NULL);

        if (isset($menu) && count($menu) > 0) {
            $menu = $menu[0];
            if ($this->_request->isPost()) {
                $this->buildMenu($menu, $this->params);
                Services::createConfigurationService()->updateMenu($menu);
                $this->redirect("/system/setting/list-menu?type=" . $menu->getType());
            }
            $this->view->menu = ControllerUtils::mapMenu($menu);

            $types = array();
            $types[] = array('code' => SA_Entity_Menu::TYPE_HEADER, 'name' => Util::translate('system.menu_type.header'));
            $types[] = array('code' => SA_Entity_Menu::TYPE_FOOTER, 'name' => Util::translate('system.menu_type.footer'));
            $types[] = array('code' => SA_Entity_Menu::TYPE_LEFT, 'name' => Util::translate('system.menu_type.left'));
            $types[] = array('code' => SA_Entity_Menu::TYPE_RIGHT, 'name' => Util::translate('system.menu_type.right'));
            $types[] = array('code' => SA_Entity_Menu::TYPE_INNER_RIGHT, 'name' => Util::translate('system.menu_type.innerRight'));
            $this->view->types = $types;

            $selectedMenuName = ($selectedType == SA_Entity_Menu::TYPE_HEADER ) ? Util::translate('system.menu_type.header') :
                    (($selectedType == SA_Entity_Menu::TYPE_FOOTER ) ? Util::translate('system.menu_type.footer') :
                            (($selectedType == SA_Entity_Menu::TYPE_LEFT ) ? Util::translate('system.menu_type.left') :
                                    (($selectedType == SA_Entity_Menu::TYPE_RIGHT ) ? Util::translate('system.menu_type.right') : Util::translate('system.menu_type.innerRight'))));
            $parentMenus = Services::createConfigurationService()->findMenus(array("pageId" => 0, "pageSize" => 0, "parentId" => 0,
                'type' => $menu->getType()));
            $parentMenus = (isset($parentMenus) && count($parentMenus) > 0) ? ControllerUtils::mapMenus($parentMenus) : array();
            array_unshift($parentMenus, array('id' => 0, 'name' => ''));

            $this->view->parentMenus = $parentMenus;
            $this->view->treeMenus = ControllerUtils::mapMenus($this->sortMenus(Services::createConfigurationService()->findMenus(array('type' => $menu->getType()))));
            $this->view->treeName = $selectedMenuName;

            $this->view->type = "update";
            $this->renderScript("setting/menu-cnu.phtml");
        }
    }

    public function upMenuAction() {
        Services::createConfigurationService()->upMenu($this->params["id"]);
        $menu = Services::createConfigurationService()->getMenu($this->params["id"], NULL);
        $menu = $menu[0];
        $this->redirect("/system/setting/list-menu?type=" . $menu->getType());
    }

    public function downMenuAction() {
        Services::createConfigurationService()->downMenu($this->params["id"]);
        $menu = Services::createConfigurationService()->getMenu($this->params["id"], NULL);
        $menu = $menu[0];
        $this->redirect("/system/setting/list-menu?type=" . $menu->getType());
    }

    public function deleteMenuAction() {
        $result = "TRUE";
        try {
            $menu = Services::createConfigurationService()->getMenu($this->params["id"], NULL);
            $menu = $menu[0];
            Services::createConfigurationService()->deleteMenu($this->params["id"]);
        } catch (Exception $exc) {
            $result = "FALSE";
        }
        $this->redirect("/system/setting/list-menu?result=" . $result . '&type=' . $menu->getType());
    }

    public function deleteAction() {
        Services::createConfigurationService()->delete($this->params["key"]);
        $this->redirect("/system/setting/list-param");
    }

    private function sortMenus($menus) {
        $retval = array();
        foreach ($menus as $parentMenu) {
            /* @var $parentMenu SA_Entity_Menu */
            if ($parentMenu->getParentId() <= 0) {
                $retval[] = $parentMenu;
                foreach ($menus as $childMenu) {
                    if ($childMenu->getParentId() == $parentMenu->getId()) {
                        $retval[] = $childMenu;
                    }
                }
            }
        }
        return $retval;
    }

    public function asyncListEntriesAction() {
        $itemsInArray = $this->getListEntryByType(array("type" => array(SA_Entity_Category::TYPE_CHILD,SA_Entity_Category::TYPE_MAIN)));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true, "data" => $itemsInArray));
    }

//    public function asyncListServicesAction() {
//        $itemsInArray = $this->getListEntryByType(array("type" => array(SA_Entity_Category::TYPE_CHILD, SA_Entity_Category::TYPE_MAIN)));
//
//        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);
//        echo json_encode(array("status" => true, "data" => $itemsInArray));
//    }

    public function asyncListQasAction() {
        $itemsInArray = $this->getListEntryByType(array("type" => array(SA_Entity_Category::TYPE_QA)));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true, "data" => $itemsInArray));
    }

    public function asyncListEventsAction() {
        $items = Services::createWorkshopService()->find(array());
        $items = isset($items) ? $items : array();

        $itemsInArray = array();
        foreach ($items as $value) {
            $item = array();
            $item ["id"] = $value->getId();
            $item ["name"] = $value->getTitle();
            $item ["parentId"] = 0;
            $itemsInArray[] = $item;
        }

        $firstElement = array("id" => "0", "parentId" => 0, "name" => Util::translate("system.menu.all_events"));
        array_unshift($itemsInArray, $firstElement);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true, "data" => $itemsInArray));
    }

    public function asyncListAlbumsAction() {
        $items = Services::createGalleryService()->find(array());
        $items = isset($items) ? $items : array();

        $itemsInArray = array();
//        foreach ($items as $value) {
//            $item = array();
//            $item ["id"] = $value->getId();
//            $item ["name"] = $value->getTitle();
//            $item ["parentId"] = 0;
//            $itemsInArray[] = $item;
//        }

        $firstElement = array("id" => "0", "parentId" => 0, "name" => Util::translate("system.menu.all_albums"));
        array_unshift($itemsInArray, $firstElement);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true, "data" => $itemsInArray));
    }

    public function asyncLoadParentMenuAction() {
        $params = $this->getRequest()->getParams();
        $filter = array("pageId" => 0,
            "pageSize" => 0,
            "parentId" => 0);

        if (isset($params['type']) && $params['type'] != "")
            $filter['type'] = $params['type'];

        $parentMenus = Services::createConfigurationService()->findMenus($filter);
        $parentMenus = (isset($parentMenus) && count($parentMenus) > 0) ? ControllerUtils::mapMenus($parentMenus) : array();
        array_unshift($parentMenus, array('id' => '0', 'name' => ''));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("success" => true, "items" => $parentMenus));
    }

    public function asyncGetMenuByTypeAction() {
        $params = $this->getRequest()->getParams();
        $selectedType = (isset($params["type"]) && $params["type"] != "") ? $params["type"] : SA_Entity_Menu::TYPE_HEADER;

        $treeName = ($selectedType == SA_Entity_Menu::TYPE_HEADER ) ? Util::translate('system.menu_type.header') :
                (($selectedType == SA_Entity_Menu::TYPE_FOOTER ) ? Util::translate('system.menu_type.footer') :
                        (($selectedType == SA_Entity_Menu::TYPE_LEFT ) ? Util::translate('system.menu_type.left') :
                                (($selectedType == SA_Entity_Menu::TYPE_RIGHT ) ? Util::translate('system.menu_type.right') : Util::translate('system.menu_type.innerRight'))));
        $treeMenus = ControllerUtils::mapMenus($this->sortMenus(Services::createConfigurationService()->findMenus(array('type' => $selectedType))));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("treeName" => $treeName, "treeMenus" => $treeMenus, "success" => true));
    }

    public function asyncUpdateMenuStatusAction() {
        $params = $this->getRequest()->getParams();
        $id = (isset($params["id"]) && $params["id"] != "") ? $params["id"] : "0";

        if ($id != "0") {
            $menu = Services::createConfigurationService()->getMenu($id, NULL);
            $menu = $menu[0];
            $menu->setStatus($params["isCheck"]);
            Services::createConfigurationService()->updateMenu($menu);
        }

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("success" => true));
    }

    public function asyncAddRemoveMenuByCategoryAction() {
        $params = $this->getRequest()->getParams();
        $result = false;

        if (isset($params['id']) && $params['id'] != "") {
            $isAddEvent = $params['isAdd'];
            $id = $params['id'];
            $type = $params['type'];
            $linkType = $params['linkType'];

            $filter = array('type' => $type, 'linkId' => $id, 'linkType' => $linkType);
            $arrResult = Services::createConfigurationService()->findMenus($filter);

            if (((!isset($arrResult) || count($arrResult) == 0 ) && $isAddEvent == '0') ||
                    (count($arrResult) > 0 && $isAddEvent == '1')) {
                $result = true;
            } else {
                try {
                    if ($isAddEvent == '0') {
                        Services::createConfigurationService()->deleteMenu($arrResult[0]->getId());
                    } else {
                        $category = Services::createCategoryService()->get($id);
                        $menu = new SA_Entity_Menu();
                        $menu->setCode("")
                                ->setType($type)
                                ->setDescription('')
                                ->setLinkId($id)
                                ->setLinkType($linkType)
                                ->setLinkUrl('')
                                ->setName($category->getName())
                                ->setLogoPath('')
                                ->setParentId(0)
                                ->setHtmlCode('')
                                ->setStatus('1')
                                ->setUpdateTime(new DateTime())
                                ->setCreateTime(new DateTime());
                        Services::createConfigurationService()->createMenu($menu);
                    }
                    $result = true;
                } catch (Exception $ex) {
                    
                }
            }
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("success" => $result));
    }

    private function getListEntryByType($filter) {
        $items = Services::createEntryService()->find($filter);
        $items = isset($items) ? $items : array();

        $itemsInArray = array();
        foreach ($items as $value) {
            $item = array();
            $item ["id"] = $value->getId();
            $item ["name"] = $value->getTitle();
            $item ["parentId"] = 0;
            $itemsInArray[] = $item;
        }

        return $itemsInArray;
    }

}
