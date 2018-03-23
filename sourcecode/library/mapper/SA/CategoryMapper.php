<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthenticationMapper
 *
 * @author ThoMC
 */
class SA_CategoryMapper implements SA_CategoryService {

    public function create(SA_Entity_Category $category) {
        $siblingCategories = $this->find(array("pageId" => "0",
            "pageSize" => "0",
            "type" => $category->getType(),
            "parentId" => $category->getParentId()));
        $orderNumber = 0;
        if (count($siblingCategories) > 0) {
            $lastSiblingCategory = $siblingCategories[count($siblingCategories) - 1];
            $orderNumber = $lastSiblingCategory->getOrderNumber() + 1;
        }
        $category->setOrderNumber($orderNumber);
        try {
            MapperUtil::getDbTable_Category()->getAdapter()->beginTransaction();
            //insert product
            $data = MapperUtil::mapObject($category, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data['id']);
            unset($data['header_menu']);
            unset($data['footer_menu']);
            unset($data['entries']);

            MapperUtil::getDbTable_Category()->insert($data);
            $retVal = MapperUtil::getDbTable_Category()->getAdapter()->lastInsertId();
            MapperUtil::getDbTable_Category()->getAdapter()->commit();
        } catch (Exception $ex) {
            MapperUtil::getDbTable_Category()->getAdapter()->rollBack();
            $retVal = 0;
            throw $ex;
        }
        return $retVal;
    }

    public function delete($categoryId) {
        $category = $this->get($categoryId);
        $filter = array();
        $filter['metric'] = "recordCount";
        $filter['categoryId'] = $category->getId();
        $itemCount = 0;
        if ($category->getType() != SA_Entity_Category::TYPE_SUPPORT) {
            $itemCount = Services::createEntryService()->find($filter);
        }

        if ($itemCount == (int) 0) {
            MapperUtil::getDbTable_Category()
                    ->delete(MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('id = ?', $categoryId));
        } else {
            throw new RuntimeException("Can not delete Category[" . $categoryId . "] , that contains items!");
        }
    }

    public function down($id) {
        $category = $this->get($id);
        $silblingCategories = $this->find(array(
            "pageId" => "0",
            "pageSize" => "0",
            "parentId" => $category->getParentId(),
            "type" => $category->getType(),
                ));
        $categoryIdx = -1;
        foreach ($silblingCategories as $category) {
            $categoryIdx++;
            if ($category->getId() == $id) {
                break;
            }
        }
        if ($categoryIdx < count($silblingCategories) - 1) {
            $targetCategory = $silblingCategories[$categoryIdx + 1];
            $this->swapCategories($category, $targetCategory);
        }
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
     *      orders: array(
     *          array(
     *              'column' => ?,  (column in ["title", "create_time"])
     *              'type' => ?     (type in ["acs", "desc"])
     *          )
     *          ex: $filter['orders'] = array();
     *              $orderItem = array( 'column' => 'title' , 'type' => 'asc' );
     *              $filter['orders'][] = $orderItem;
     *      ),
     *      productId, entryId, supplierId,
     *      type, code
     *      parentId,
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_Category
     *      - case2: Interger
     */
    public function find($filter) {
        if (key_exists("type", $filter) && $filter["type"] == SA_Entity_Category::TYPE_SERVICE) {
            return $this->findServiceCategory($filter);
        }

        $retVal = null;
        $selectHeader = MapperUtil::getDbTable_Menu()->select()
                ->from(array('m' => 'sa_menu'), array('link_id',
                    'header_id' => 'id'))
                ->where(MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('type = ?', SA_Entity_Menu::TYPE_HEADER) . ' AND ' .
                MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('link_type = ?', SA_Entity_Menu::LINK_TYPE_SERVICE_CATEGORY));

        $selectFooter = MapperUtil::getDbTable_Menu()->select()
                ->from(array('m' => 'sa_menu'), array('link_id',
                    'footer_id' => 'id'))
                ->where(MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('type = ?', SA_Entity_Menu::TYPE_FOOTER) . ' AND ' .
                MapperUtil::getDbTable_Menu()->getAdapter()->quoteInto('link_type = ?', SA_Entity_Menu::LINK_TYPE_SERVICE_CATEGORY));
        //'footer_menu' => 'CASE WHEN (type = \'' . SA_Entity_Menu::TYPE_FOOTER . '\' ) THEN 1 ELSE 0 end'));

        $select = MapperUtil::getDbTable_Category()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_category'))
                ->joinLeft(array('p1' => new Zend_Db_Expr('(' . $selectHeader . ')')), 'p.id = p1.link_id', array('header_menu' => 'CASE WHEN (header_id IS NULL OR header_id = \'\' ) THEN \'0\' ELSE \'1\' end'))
                ->joinLeft(array('p2' => new Zend_Db_Expr('(' . $selectFooter . ')')), 'p.id = p2.link_id', array('footer_menu' => 'CASE WHEN (footer_id IS NULL OR footer_id = \'\' ) THEN \'0\' ELSE \'1\' end'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">

        $condition = "1 = 1";
        if (key_exists("type", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('p.type = ?', $filter["type"]);
        }
        if (key_exists("code", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('p.code = ?', $filter["code"]);
        }
        if (key_exists("categoryIds", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("p.id IN (?)", $filter["categoryIds"]);
        }
        if (key_exists("parentId", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('p.parent_id = ?', $filter["parentId"]);
        }
        if (key_exists("parentCode", $filter)) {
            $parentQuery = "( SELECT id FROM sa_category WHERE "
                    . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('code = ?', $filter["parentCode"])
                    . " )";
            $condition .= ' AND p.parent_id IN ' . $parentQuery;
        }
        if (key_exists("entryId", $filter)) {
            $categoryIdsSql = "( SELECT category_id FROM sa_entry WHERE "
                    . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('id = ?', $filter["entryId"])
                    . " )";
            $condition .= ' AND p.id IN ' . $categoryIdsSql;
        }
        if (key_exists("ignoreId", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('p.id != ?', $filter["ignoreId"]);
        }

        //</editor-fold>
        $select->where($condition);
        //<editor-fold desc="order" defaultstate="collapsed">
        $order = array();
        if (key_exists("orders", $filter)) {
            foreach ($filter['orders'] as $orderItem) {
                $order[] = "p." . $orderItem['column'] . " " . $orderItem['type'];
            }
        }
        $order[] = 'p.order_number asc ';
        $select->order($order);
        //</editor-fold>
//        var_dump($select->assemble());
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "recordCount" || $metric == "pageCount") {
                $select = MapperUtil::getDbTable_Category()->select()
                        ->setIntegrityCheck(false)
                        ->from(array('p0' => new Zend_Db_Expr('(' . $select . ')')), array('COUNT' => 'COUNT(*)'));
            }
        } elseif (key_exists("pageId", $filter) && key_exists("pageSize", $filter)) {
            if ($filter["pageId"] != 0 || $filter["pageSize"] != 0) {
                $select->limitPage($filter['pageId'] + 1, $filter['pageSize']);
            }
        }
        //</editor-fold>
        //<editor-fold desc="result" defaultstate="collapsed">
        if ($metric == 'recordCount') {
            //$temp = $select . " ";
            $row = MapperUtil::getDbTable_Category()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'pageCount') {
            $row = MapperUtil::getDbTable_Category()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = MapperUtil::getDbTable_Category()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $retVal[] = MapperUtil::toObject("SA_Entity_Category", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    public function findServiceCategory($filter) {
        $retVal = null;
        $select = MapperUtil::getDbTable_Category()->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'sa_category'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";
        $condition.= " AND (" . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('c.type = ?', SA_Entity_Category::TYPE_MAIN);
        $condition.= " OR " . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('c.type = ?', SA_Entity_Category::TYPE_CHILD) . ")";

        if (key_exists("code", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('c.code = ?', $filter["code"]);
        }
        if (key_exists("categoryIds", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("c.id IN (?)", $filter["categoryIds"]);
        }
        if (key_exists("parentId", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('c.parent_id = ?', $filter["parentId"]);
        }

        //</editor-fold>
        $select->where($condition);

        $select = MapperUtil::getDbTable_Category()->select()
                ->setIntegrityCheck(false)
                ->from(array('p1' => new Zend_Db_Expr('(' . $select . ')')))
                ->joinLeft(array('p2' => new Zend_Db_Expr('(' . $select . ')')), 'p1.parent_id = p2.id', array('parent_order' => 'p2.order_number'));

        $select = MapperUtil::getDbTable_Category()->select()
                ->setIntegrityCheck(false)
                ->from(array('temp' => new Zend_Db_Expr('(' . $select . ')')), array('id', 'name' => 'CASE WHEN (type = \'' . SA_Entity_Category::TYPE_CHILD . '\') THEN CONCAT(\'-- \', name) ELSE name END',
            'description', 'image_path', 'order_number',
            'create_time', 'type', 'update_time', 'parent_id', 'code',
            'parent_order' => 'case when (parent_order is null) then order_number else parent_order end'));

        $select = MapperUtil::getDbTable_Category()->select()
                ->setIntegrityCheck(false)
                ->from(array('t' => new Zend_Db_Expr('(' . $select . ')')), array('id', 'name', 'description', 'image_path', 'order_number',
            'create_time', 'type', 'update_time', 'parent_id', 'code'));

        //<editor-fold desc="order" defaultstate="collapsed">
        $order = array();
        $order[] = "t.parent_order asc";
        $order[] = "t.type desc";
        $order[] = 't.order_number asc ';
        $select->order($order);
        //</editor-fold>
//        var_dump($select->assemble());
        //<editor-fold desc="result" defaultstate="collapsed">
        $retVal = array();
        $resultSet = MapperUtil::getDbTable_Category()->fetchAll($select);
        foreach ($resultSet as $row) {
            if (isset($row->id) && $row->id != null) {
                $retVal[] = MapperUtil::toObject("SA_Entity_Category", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    public function get($categoryId) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Category()->fetchRow(MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("id = ?", $categoryId));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Category", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }
    
    public function getRewriteUrl($rewriteUrl) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Category()->fetchRow(MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("rewrite_url = ?", $rewriteUrl));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Category", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function getCode($categoryCode) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Category()->fetchRow(MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("code = ?", $categoryCode));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Category", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function up($id) {
        $category = $this->get($id);
        $silblingCategories = $this->find(array(
            "pageId" => "0",
            "pageSize" => "0",
            "parentId" => $category->getParentId(),
            "type" => $category->getType(),
                ));
        $categoryIdx = -1;
        foreach ($silblingCategories as $category) {
            $categoryIdx++;
            if ($category->getId() == $id) {
                break;
            }
        }
        if ($categoryIdx > 0) {
            $targetCategory = $silblingCategories[$categoryIdx - 1];
            $this->swapCategories($category, $targetCategory);
        }
    }

    public function update(SA_Entity_Category $category) {
        $data = MapperUtil::mapObject($category, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        unset($data['entries']);
        unset($data['header_menu']);
        unset($data['footer_menu']);
        //update
        MapperUtil::getDbTable_Category()->update($data, MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("id = ?", $category->getId()));
    }

    public function getMaxOrderByParentId($parentCateId) {
        $select = MapperUtil::getDbTable_Category()->select()
                ->from(array('p' => 'sa_category'), array(new Zend_Db_Expr("MAX(order_number) AS maxOrder")))
                ->where(MapperUtil::getDbTable_Category()->getAdapter()->quoteInto('p.parent_id = ?', $parentCateId));

        $resultSet = MapperUtil::getDbTable_Category()->fetchAll($select);
        if (isset($resultSet) && count($resultSet) > 0) {
            return $resultSet[0]['maxOrder'];
        }
        return '0';
    }

    public function serialize($categories, $currentCategory = NULL, $level = 0) {
        $retval = array();
        /* @var $category SA_Entity_Category */
        foreach ($categories as $category) {
            $nextCategory = null;
            if ($currentCategory == NULL) {
                if ($category->getParentId() <= 0) {
                    $nextCategory = $category;
                }
            } else {
                if ($category->getParentId() == $currentCategory->getId()) {
                    $nextCategory = $category;
                }
            }
            if ($nextCategory != null) {
                $name = "";
                for ($index = 0; $index < $level; $index++) {
                    $name.="-- ";
                }
                $nextCategory->setName($name . $nextCategory->getName());
                $retval[] = $nextCategory;
                $retvalCategories = $this->serialize($categories, $nextCategory, $level + 1);
                foreach ($retvalCategories as $retvalCategory) {
                    $retval[] = $retvalCategory;
                }
            }
        }
        return $retval;
    }

    public function createLocation(SA_Entity_Location $location) {
        $leftPosition = 0;
        $rightPosition = 0;
        $parentNode = $this->getLocation($location->getParentId());

        try {
            MapperUtil::getDbTable_Location()->getAdapter()->beginTransaction();

            if (isset($parentNode) && count($parentNode) > 0) {
                $leftPosition = $parentNode->getRgt();
                $rightPosition = $parentNode->getRgt() + 1;

                //update other nodes on right side of array
                $data = array('rgt' => new Zend_Db_Expr('rgt + 2'));
                $where = "rgt >= " . $parentNode->getRgt();
                MapperUtil::getDbTable_Location()->update($data, $where);

                $data = array('lft' => new Zend_Db_Expr('lft + 2'));
                $where = "lft >= " . $parentNode->getRgt();
                MapperUtil::getDbTable_Location()->update($data, $where);
            } else {
                $select = MapperUtil::getDbTable_Location()->select()
                        ->from(array('l' => 'sa_location'), array(new Zend_Db_Expr("MAX(rgt) AS maxRight")));

                $resultSet = MapperUtil::getDbTable_Location()->fetchAll($select);
                $maxRight = (isset($resultSet) && count($resultSet) > 0) ? intval($resultSet[0]['maxRight']) : 0;

                $leftPosition = $maxRight + 1;
                $rightPosition = $maxRight + 2;
            }

            //insert new node
            $location->setLft($leftPosition)
                    ->setRgt($rightPosition);

            $data = MapperUtil::mapObject($location, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data['id']);
            MapperUtil::getDbTable_Location()->insert($data);

            MapperUtil::getDbTable_Location()->getAdapter()->commit();
        } catch (Exception $e) {
            MapperUtil::getDbTable_Location()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function deleteLocation($locationId) {
        $deleteNode = $this->getLocation($locationId);
        $leftPosition = $deleteNode->getLft();
        $rightPosition = $deleteNode->getRgt();

        try {
            MapperUtil::getDbTable_Location()->getAdapter()->beginTransaction();

            //delete node + all child nodes
            $where = MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('lft >= ?', $leftPosition);
            $where .= ' AND ' . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('rgt <= ?', $rightPosition);
            MapperUtil::getDbTable_Location()
                    ->delete($where);

            //update other nodes on right side of array
            $data = array('lft' => new Zend_Db_Expr('lft - ' . ($rightPosition - $leftPosition + 1)));
            $where = MapperUtil::getDbTable_Location()->getAdapter()->quoteInto("lft > ?", $rightPosition);
            MapperUtil::getDbTable_Location()->update($data, $where);

            $data = array('rgt' => new Zend_Db_Expr('rgt - ' . ($rightPosition - $leftPosition + 1)));
            $where = MapperUtil::getDbTable_Location()->getAdapter()->quoteInto("rgt > ?", $rightPosition);
            MapperUtil::getDbTable_Location()->update($data, $where);


            MapperUtil::getDbTable_Location()->getAdapter()->commit();
        } catch (Exception $e) {
            MapperUtil::getDbTable_Location()->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function downLocation($id) {
        $location = $this->getLocation($id);
        $silblingLocations = $this->findLocation(array(
            "pageId" => "0",
            "pageSize" => "0",
            "parentId" => $location->getParentId(),
            "moreLeftPosition" => $location->getLft(),
            "orders" => array(array('column' => 'lft', 'type' => 'asc'))));

        if (isset($silblingLocations) && count($silblingLocations) > 0)
            $this->swapLocations($silblingLocations[0], $location);
    }

    public function findLocation($filter) {
        $retVal = null;
        $select = MapperUtil::getDbTable_Location()->select()
                ->setIntegrityCheck(false)
                ->from(array('l' => 'sa_location'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";
//        if (key_exists("code", $filter)) {
//            $condition .= ' AND ' . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('l.code = ?', $filter["code"]);
//        }
        if (key_exists("parentId", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('l.parent_id = ?', $filter["parentId"]);
        }
//        if (key_exists("parentCode", $filter)) {
//            $parentQuery = "( SELECT id FROM sa_location WHERE "
//                    . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('code = ?', $filter["parentCode"])
//                    . " )";
//            $condition .= ' AND l.parent_id IN ' . $parentQuery;
//        }

        if (key_exists("lessLeftPosition", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('l.lft < ?', $filter["lessLeftPosition"]);
        }

        if (key_exists("moreLeftPosition", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('l.lft > ?', $filter["moreLeftPosition"]);
        }

        if (key_exists("keyword", $filter)) {
            $condition .= ' AND ' . MapperUtil::getDbTable_Location()->getAdapter()->quoteInto('l.name LIKE ?', '%' . $filter["keyword"] . '%');
        }


        $select->where($condition);

//        $select = MapperUtil::getDbTable_Location()->select()
//                ->setIntegrityCheck(false)
//                ->from(array('node' => new Zend_Db_Expr('(' . $select . ')')), array('node.id', 'node.parent_id', 'node.lft',
//                    'node.rgt', 'node.description', 'node.name', 'node.code',
//                    'node.display_name'))
//               ;
        //</editor-fold>
        //<editor-fold desc="order" defaultstate="collapsed">
        $order = array();
        if (key_exists("orders", $filter)) {
            foreach ($filter['orders'] as $orderItem) {
                $order[] = 'l.' . $orderItem['column'] . " " . $orderItem['type'];
            }
        }
        $select->order($order);
        //</editor-fold>
//        var_dump($select->assemble());
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "recordCount" || $metric == "pageCount") {
                $select = MapperUtil::getDbTable_Location()->select()
                        ->setIntegrityCheck(false)
                        ->from(array('p0' => new Zend_Db_Expr('(' . $select . ')')), array('COUNT' => 'COUNT(*)'));
            }
        } elseif (key_exists("pageId", $filter) && key_exists("pageSize", $filter)) {
            if ($filter["pageId"] != 0 || $filter["pageSize"] != 0) {
                $select->limitPage($filter['pageId'] + 1, $filter['pageSize']);
            }
        }
        //</editor-fold>
        //<editor-fold desc="result" defaultstate="collapsed">
        if ($metric == 'recordCount') {
            //$temp = $select . " ";
            $row = MapperUtil::getDbTable_Location()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'pageCount') {
            $row = MapperUtil::getDbTable_Location()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = MapperUtil::getDbTable_Location()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $retVal[] = MapperUtil::toObject("SA_Entity_Location", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    public function getLocation($locationId) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Location()->fetchRow(MapperUtil::getDbTable_Location()->getAdapter()->quoteInto("id = ?", $locationId));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Location", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function updateLocation($location) {
        try {
            $data = MapperUtil::mapObject($location, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data["id"]);
            //update
            MapperUtil::getDbTable_Location()->update($data, MapperUtil::getDbTable_Location()->getAdapter()->quoteInto("id = ?", $location->getId()));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function upLocation($id) {
        $location = $this->getLocation($id);
        $silblingLocations = $this->findLocation(array(
            "pageId" => "0",
            "pageSize" => "0",
            "parentId" => $location->getParentId(),
            "lessLeftPosition" => $location->getLft(),
            "orders" => array(array('column' => 'lft', 'type' => 'desc'))));
        if (isset($silblingLocations) && count($silblingLocations) > 0)
            $this->swapLocations($location, $silblingLocations[0]);
    }

    //Utils
    private function swapCategories(SA_Entity_Category $category1, SA_Entity_Category $category2) {
        if ($category1->getType() != $category2->getType()) {
            return;
        }
        //create where condition
        $where1 = MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("id = ?", $category1->getId())
                . " AND " . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("order_number = ?", $category1->getOrderNumber());
        $where2 = MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("id = ?", $category2->getId())
                . " AND " . MapperUtil::getDbTable_Category()->getAdapter()->quoteInto("order_number = ?", $category2->getOrderNumber());
        //update
        MapperUtil::getDbTable_Category()->update(array("order_number" => $category2->getOrderNumber()), $where1);
        MapperUtil::getDbTable_Category()->update(array("order_number" => $category1->getOrderNumber()), $where2);
    }

    private function swapLocations($locationUp, $locationDown) {

        try {
            MapperUtil::getDbTable_Location()->getAdapter()->beginTransaction();

            //update down node to minus position 
            $data = array('lft' => new Zend_Db_Expr('lft - ' . ($locationDown->getRgt() + 1)),
                'rgt' => new Zend_Db_Expr('rgt - ' . ($locationDown->getRgt() + 1)));
            $where = "(lft >= " . $locationDown->getLft() . ")";
            $where .= " AND (rgt <= " . $locationDown->getRgt() . ")";
            MapperUtil::getDbTable_Location()->update($data, $where);

            //update up node to right position 
            $data = array('lft' => new Zend_Db_Expr('lft - ' . ($locationUp->getLft() - $locationDown->getLft())),
                'rgt' => new Zend_Db_Expr('rgt - ' . ($locationUp->getLft() - $locationDown->getLft())));
            $where = "(lft >= " . $locationUp->getLft() . ")";
            $where .= " AND (rgt <= " . $locationUp->getRgt() . ")";
            MapperUtil::getDbTable_Location()->update($data, $where);

            //update down node to right position 
            $data = array('lft' => new Zend_Db_Expr('lft + ' . ($locationDown->getRgt() + $locationUp->getRgt() - $locationUp->getLft() + 2)),
                'rgt' => new Zend_Db_Expr('rgt + ' . ($locationDown->getRgt() + $locationUp->getRgt() - $locationUp->getLft() + 2)));
            $where = "lft < 0";
            MapperUtil::getDbTable_Location()->update($data, $where);

            MapperUtil::getDbTable_Location()->getAdapter()->commit();
        } catch (Exception $e) {
            MapperUtil::getDbTable_Location()->getAdapter()->rollBack();
            throw $e;
        }
    }

}
