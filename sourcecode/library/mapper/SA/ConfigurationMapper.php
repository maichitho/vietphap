<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigurationMapper
 *
 * @author Sililab
 * @created 2014-05-06 17:49:30
 */
class SA_ConfigurationMapper implements SA_ConfigurationService
{

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    //--------------------------------------------------------------------------
    //  Implement N Override

    public function create(SA_Entity_Param $param)
    {
        $data = MapperUtil::mapObject($param, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data['id']);
        MapperUtil::getDbTable_Param()->insert($data);
        $retVal = MapperUtil::getDbTable_Param()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete($key)
    {
        try {
            $where = MapperUtil::getDbTable_Param()->getAdapter()->quoteInto('key2 = ?', $key);
            MapperUtil::getDbTable_Param()->delete($where);
        } catch (Exception $e) {
            var_dump($e);
            throw $e;
        }
    }

    public function find($filter)
    {
        $retVal = array();
        $select = MapperUtil::getDbTable_Param()->select()
            ->from(array("p" => "sa_param"));
        if (key_exists("keyPrefix", $filter)) {
            $select->where(MapperUtil::getDbTable_Param()->getAdapter()->quoteInto("key2 LIKE ?", '%' . $filter["keyPrefix"] . '%'));
        }
        if (key_exists("paramType", $filter)) {
            $select->where(MapperUtil::getDbTable_Param()->getAdapter()->quoteInto("param_type = ?", $filter["paramType"]));
        }

        //<editor-fold desc="order" defaultstate="collapsed">
        $order = array();
        if (key_exists("orders", $filter)) {
            foreach ($filter['orders'] as $orderItem) {
                $order[] = $orderItem['column'] . " " . $orderItem['type'];
            }
        }
        $order[] = 'key2 DESC ';
        $select->order($order);
        //</editor-fold>

        $resultSet = MapperUtil::getDbTable_Param()->fetchAll($select);
        foreach ($resultSet as $row) {
            $retVal[] = MapperUtil::toObject("SA_Entity_Param", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    protected function get_server_var($id)
    {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    public function get($key)
    {
      
        $retVal = null;
       
        $where = MapperUtil::getDbTable_Param()->getAdapter()->quoteInto("key2 = ?", $key);
        $row = MapperUtil::getDbTable_Param()->fetchRow($where);
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Param", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }


        return $retVal;
    }

    public function update(SA_Entity_Param $param)
    {
        if ($param != null) {
            $data = MapperUtil::mapObject($param, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data['id']);
            unset($data['key2']);
            $where = MapperUtil::getDbTable_Param()->getAdapter()->quoteInto('key2 = ?', $param->getKey2());
            MapperUtil::getDbTable_Param()->update($data, $where);
        }
    }

    public function getCategoryTreeForNavigation($id, $type)
    {
        $navigations = array();

        if ($type != SA_Entity_Menu::LINK_TYPE_ALBUM && $type != SA_Entity_Menu::LINK_TYPE_EVENT) {
            switch ($type) {
                case SA_Entity_Menu::LINK_TYPE_QA:
                    $firstItemNameInArray = Util::translate("navigation.qa_list");
                    $firstItemLinkInArray = "";
                    break;
                default:
                    $firstItemNameInArray = "";
                    $firstItemLinkInArray = "";
            }

            while ($id > 0) {
                $cate = Services::createCategoryService()->get($id);

                // get rewrite url of category by menu
                $menuFilter = array("pageSize" => 0,
                    "pageId" => 0,
                    "linkId" => $id);
                $link = "";
                $menus = Services::createConfigurationService()->findMenus($menuFilter);
                if (count($menus) > 0) {
                    $link = $menus[0]->getRewriteUrl();
                } else {
                    // for benhkhotho
                    if ($cate->getParentId() > 0) {
                        $menuFilter = array("pageSize" => 0,
                            "pageId" => 0,
                            "linkId" => $cate->getParentId());
                        $menus = Services::createConfigurationService()->findMenus($menuFilter);
                        if (count($menus) > 0) {
                            $link = $menus[0]->getRewriteUrl();
                        }
                    }
                }

//                $name = Util::toFriendlyString($cate->getName());
                $navigations[] = array("name" => $cate->getName(), "link" => '/' . $link);
                $id = $cate->getParentId();
            }

            $navigations = array_reverse($navigations);

            if ($firstItemNameInArray != "") {
                array_unshift($navigations, array("name" => $firstItemNameInArray, "link" => $firstItemLinkInArray));
            }
            array_unshift($navigations, array("name" => Util::translate("home.main_page"), "link" => "/"));
        }
        return $navigations;
    }

    /**
     *
     * @param SA_Entity_Menu $menu
     */
    public function createMenu(SA_Entity_Menu $menu)
    {
        $siblingMenus = $this->findMenus(array("pageId" => "0",
            "pageSize" => "0",
            "parentId" => $menu->getParentId()));
        $orderNumber = 1;
        if (count($siblingMenus) > 0) {
            $lastSiblingMenu = $siblingMenus[count($siblingMenus) - 1];
            $orderNumber = $lastSiblingMenu->getOrderNumber() + 1;
        }
        $menu->setOrderNumber($orderNumber);

        $data = MapperUtil::mapObject($menu, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data['id']);
        if ($data['link_id'] == "")
            $data['link_id'] = "0";
        MapperUtil::getDbTable_Menu()->insert($data);
        $retVal = MapperUtil::getDbTable_Menu()->getAdapter()->lastInsertId();
        return $retVal;
    }

    /**
     *
     * @param type $id
     */
    public function deleteMenu($id)
    {
        $childrenCount = $this->findMenus(array(
            "metric" => "recordCount",
            "parentId" => $id));
        if ($childrenCount > 0) {
            throw new Exception("Can NOT delete menu when its submenus are not empty");
        }
        $where = MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("id = ?", $id);
        MapperUtil::getDbTable_Menu()->delete($where);
    }

    /**
     *
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null
     *              + metric = "recordCount" (require: not exist pageId & pageSize)
     *              + metric = "pageCount" (require: pageSize)
     * @options
     *      parentId,
     *      type,
     *      status,
     *      languageCode
     * @return
     *      - case1: array of SA_Entity_Menu
     *      - case2: array of Interger
     */
    public function findMenus($filter)
    {
        $retVal = null;
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Menu()->select()
                ->from(array("m" => "sa_menu"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Menu()->select()
                ->from(array("m" => "sa_menu"));
        }
        $condition = "1=1 ";
        if (key_exists("parentId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("m.parent_id = ?", $filter["parentId"]);
        }
        if (key_exists("type", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("m.type = ?", $filter["type"]);
        }
        if (key_exists("status", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("m.status >= ?", $filter["status"]);
        }
        if (key_exists("linkId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("m.link_id = ?", $filter["linkId"]);
        }
        if (key_exists("linkType", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("m.link_type = ?", $filter["linkType"]);
        }
        if (key_exists("languageCode", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('m.language_code = ?', $filter["languageCode"]);
        }
        $select->where($condition);
        //var_dump($select->assemble());
        if (key_exists("metric", $filter)) {
            $row = MapperUtil::getDbTable_Menu()->fetchRow($select);
            $recordsCount = $row->COUNT;
            if ($filter["metric"] == "pageCount") {
                $retVal = Util::recordsCountToPagesCount($recordsCount, $filter["pageSize"]);
            } else {
                $retVal = $recordsCount;
            }
        } else {
            $select->order(array("order_number ASC"));
            if ($filter["pageId"] != 0 || $filter["pageSize"] != 0) {
                $select->limitPage($filter["pageId"] + 1, $filter["pageSize"]);
            }
            $resultSet = MapperUtil::getDbTable_Menu()->fetchAll($select);
            foreach ($resultSet as $row) {
                $menu = MapperUtil::toObject("SA_Entity_Menu", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $menu;
            }
        }
        return $retVal;
    }

    /**
     *
     * @param type $id
     * @return list of SA_Entity_Menu
     */
    public function getMenu($id, $languageCode)
    {
        $retVal = array();
        $select = MapperUtil::getDbTable_Menu()->select()
            ->setIntegrityCheck(false)
            ->from(array('m' => 'sa_menu'));
        $condition = "1 = 1";
        $condition .= ' AND ' . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('m.id = ?', $id);
        if (isset($languageCode) && $languageCode != NULL) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('m.language_code = ?', $languageCode);
        }
        $select->where($condition);
        $resultSet = MapperUtil::getDbTable_Menu()->fetchAll($select);
        foreach ($resultSet as $row) {
            if (isset($row->id) && $row->id != null) {
                $entity = MapperUtil::toObject("SA_Entity_Menu", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $entity;
            }
        }
        return $retVal;
    }

    /**
     *
     * @param type $url
     * @return list of SA_Entity_Menu
     */
    public function getMenuByRewriteUrl($rewriteUrl)
    {
        $retVal = null;
        $row = MapperUtil::getDbTable_Menu()->fetchRow(MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("rewrite_url = ?", $rewriteUrl));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Menu", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    /**
     *
     * @param SA_Entity_Menu $menu
     */
    public function updateMenu(SA_Entity_Menu $menu)
    {
        if ($menu != null) {
            $data = MapperUtil::mapObject($menu, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data["id"]);
//            unset( $data["order_number"] );
            unset($data["language_code"]);
            $where = MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('id = ?', $menu->getId());
            //$where .= " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('language_code = ?', $menu->getLanguageCode());
            MapperUtil::getDbTable_Menu()->update($data, $where);
        }
    }

    /**
     *
     * @param type $id
     */
    public function downMenu($id)
    {
        $menus = $this->getMenu($id, NULL);
        foreach ($menus as $menu) {
            $silblingMenus = $this->findMenus(array(
                "pageId" => "0",
                "pageSize" => "0",
                "languageCode" => $menu->getLanguageCode(),
                "parentId" => $menu->getParentId()
            ));
            $menuIdx = -1;
            foreach ($silblingMenus as $silblingmenu) {
                $menuIdx++;
                if ($silblingmenu->getId() == $id) {
                    break;
                }
            }
            if ($menuIdx < count($silblingMenus) - 1) {
                $targetMenu = $silblingMenus[$menuIdx + 1];
                $this->swapMenus($menu, $targetMenu);
            }
        }
    }

    /**
     *
     * @param type $id
     */
    public function upMenu($id)
    {
        $menus = $this->getMenu($id, NULL);
        foreach ($menus as $menu) {
            $silblingMenus = $this->findMenus(array(
                "pageId" => "0",
                "pageSize" => "0",
                "languageCode" => $menu->getLanguageCode(),
                "parentId" => $menu->getParentId()));
            $menuIdx = -1;
            foreach ($silblingMenus as $menu) {
                $menuIdx++;
                if ($menu->getId() == $id) {
                    break;
                }
            }
            if ($menuIdx > 0) {
                $targetMenu = $silblingMenus[$menuIdx - 1];
                $this->swapMenus($menu, $targetMenu);
            }
        }
    }

    //--------------------------------------------------------------------------
    //  Utils

    private function swapMenus(SA_Entity_Menu $menu1, SA_Entity_Menu $menu2)
    {
        //create where condition
        $where1 = MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("id = ?", $menu1->getId())
            . " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("language_code = ?", $menu1->getLanguageCode())
            . " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("order_number = ?", $menu1->getOrderNumber());
        $where2 = MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("id = ?", $menu2->getId())
            . " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("language_code = ?", $menu2->getLanguageCode())
            . " AND " . MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto("order_number = ?", $menu2->getOrderNumber());
        //update
        MapperUtil::getDbTable_Menu()->update(array("order_number" => $menu2->getOrderNumber()), $where1);
        MapperUtil::getDbTable_Menu()->update(array("order_number" => $menu1->getOrderNumber()), $where2);
    }

}
