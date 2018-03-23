<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductMapper
 *
 * @author Sililab
 * @created 2014-05-07 09:01:10
 */
class SA_ProductMapper implements SA_ProductService {

    //--------------------------------------------------------------------------
    //  Members
    private $dbTable;
    private $dbTableProductNCategory;
    private $dbTableCategory;
    private $dbTableProductImage;
    private $dbTableProductRelation;

    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    private function getDbTable() {
        if ($this->dbTable == null) {
            $this->dbTable = new SA_DbTable_Product();
        }
        return $this->dbTable;
    }

    private function getDbTableProductNCategory() {
        if ($this->dbTableProductNCategory == null) {
            $this->dbTableProductNCategory = new SA_DbTable_ProductNCategory();
        }
        return $this->dbTableProductNCategory;
    }

    private function getDbTableProductRelation() {
        if ($this->dbTableProductRelation == null) {
            $this->dbTableProductRelation = new SA_DbTable_RelatedProduct();
        }
        return $this->dbTableProductRelation;
    }

    private function getDbTableCategory() {
        if ($this->dbTableCategory == null) {
            $this->dbTableCategory = new SA_Table_Category();
        }
        return $this->dbTableCategory;
    }

    private function getDbTableProductImage() {
        if ($this->dbTableProductImage == null) {
            $this->dbTableProductImage = new SA_DbTable_ProductImage();
        }
        return $this->dbTableProductImage;
    }

    //--------------------------------------------------------------------------
    //  Method binding
    //--------------------------------------------------------------------------
    //  Implement N Override
    /**
     * 
     * @param SA_Entity_Product $product
     * @require
     *      - categoryId, code, createTime, updateTime
     * @option
     *      - productImages (create new the list of image of product)
     *      - productConsts (create new the list of link(product N const))
     * @return 
     *      - id of new product
     */
    public function create(SA_Entity_Product $product) {
        if ($product->getCode() == null || $product->getName() == null) {
            throw new RuntimeException("Uncondition for create Product !");
        }
        try {
            $this->getDbTable()->getAdapter()->beginTransaction();
            //insert product
            $data = $this->productToArray($product);
            //insert
            $retVal = $this->getDbTable()->insert($data);
            //image
            if ($product->getProductImages() != null) {
                foreach ($product->getProductImages() as $productImage) {
                    /* @var $productImage SA_Entity_ProductImage */
                    $productImage->setProductId($retVal["id"]);
                    $productImageArrayData = $this->productImageToArray($productImage);
                    $this->getDbTableProductImage()->insert($productImageArrayData);
                }
            }
            //const
            if ($product->getProductConsts() != null) {
                foreach ($product->getProductConsts() as $productConst) {
                    /* @var $productConst SA_Entity_ProductConst */
                    $productNConstArrayData = array(
                        "product_id" => $retVal,
                        "product_const_id" => $productConst->getId(),
                        "create_time" => DateTimeUtil::toSqlString(new DateTime())
                    );
                    $this->getDbTableProductNConst()->insert($productNConstArrayData);
                }
            }
            //
            $this->getDbTable()->getAdapter()->commit();
        } catch (Exception $ex) {
            $this->getDbTable()->getAdapter()->rollBack();
            throw $ex;
        }
        return $retVal;
    }

    /**
     * 
     * @param SA_Entity_ProductCategory $category
     * @require
     *      - name, orderNumber, createTime, updateTime
     * @return
     *      - id of new product category
     */
    public function createCategory(SA_Entity_ProductCategory $category) {
        $siblingCategories = $this->findCategories(array("pageId" => "0",
            "pageSize" => "0",
            "type" => $category->getType(),
            "languageCode" => $category->getLanguageCode(),
            "parentId" => $category->getParentId()));
        $orderNumber = 0;
        if (count($siblingCategories) > 0) {
            $lastSiblingCategory = $siblingCategories[count($siblingCategories) - 1];
            $orderNumber = $lastSiblingCategory->getOrderNumber() + 1;
        }
        $category->setOrderNumber($orderNumber);
        try {
            $this->getDbTableCategory()->getAdapter()->beginTransaction();
            //insert product
            $data = $this->productCategoryToArray($category);
            //insert
            $retVal = $this->getDbTableCategory()->insert($data);
            $this->getDbTableCategory()->getAdapter()->commit();
        } catch (Exception $ex) {
            $this->getDbTableCategory()->getAdapter()->rollBack();
            throw $ex;
        }
        return $retVal;
    }

    /**
     * 
     * @param type $id (id of product)
     */
    public function delete($id) {
        try {
            $this->getDbTable()->getAdapter()->beginTransaction();
            //delete product
            $this->getDbTable()
                    ->delete($this->getDbTable()->getAdapter()->quoteInto('id = ?', $id));
            //delete product N const
            $this->getDbTableProductNConst()
                    ->delete($this->getDbTableProductNConst()->getAdapter()->quoteInto('product_id = ?', $id));
            //delete product N category
            $this->getDbTableProductNCategory()
                    ->delete($this->getDbTableProductNCategory()->getAdapter()->quoteInto('product_id = ?', $id));
            //delete related product           
            $where = $this->getDbTableProductRelation()->getAdapter()->quoteInto('product_id = ?', $id);
            $where .=' OR ' . $this->getDbTableProductRelation()->getAdapter()->quoteInto('related_product_id = ?', $id);
            $this->getDbTableProductRelation()
                    ->delete($where);
            //delete product image            
            $this->getDbTableProductImage()
                    ->delete($this->getDbTableProductImage()->getAdapter()->quoteInto('product_id = ?', $id));
            //
            $this->getDbTable()->getAdapter()->commit();
        } catch (Exception $ex) {
            $this->getDbTable()->getAdapter()->rollBack();
            throw $ex;
        }
    }

    /**
     * 
     * @param type $categoryId
     * @tutorial only can delete category when category haven't any child (product)
     */
    public function deleteCategory($categoryId) {
        $filter = array();
        $filter['categoryIds'] = array($categoryId);
        $filter['metric'] = "record-count";
        $childCount = $this->find($filter);
        //
        if ($childCount == (int) 0) {
            $this->getDbTableCategory()
                    ->delete($this->getDbTableCategory()->getAdapter()->quoteInto('id = ?', $categoryId));
        } else {
            throw new RuntimeException("Can not delete Category[" . $categoryId . "] !");
        }
    }

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @option
     *      keyword
     *      productName, productCode,
     *      categoryIds: array(),
     *      languageCode,
     *      productType,     
     *      orderNumber,    
     *      relateOfProductId,
     *      orders: array(
     *          array(
     *              'column' => ?,  (column in ["title", "create_time"])
     *              'type' => ?     (type in ["acs", "desc"])
     *          )
     *          ex: $filter['orders'] = array();
     *              $orderItem = array( 'column' => 'title' , 'type' => 'asc' );
     *              $filter['orders'][] = $orderItem;
     *      ),
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of PS_Entity_Product
     *      - case2: Interger
     */
    public function find($filter) {
        $retVal = null;
        $select = $this->getDbTable()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";
        if (key_exists("keyword", $filter)) {
            $condition .= ' AND ( ' . $this->getDbTable()->getAdapter()->quoteInto('p.name LIKE ?', '%' . $filter["keyword"] . '%')
                    . " OR " . $this->getDbTable()->getAdapter()->quoteInto('p.code LIKE ?', '%' . $filter["keyword"] . '%') . ' )';
        }
        if (key_exists("productName", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.name LIKE ?', '%' . $filter["productName"] . '%');
        }
        if (key_exists("productCode", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.code = ?', $filter["productCode"]);
        }
        if (key_exists("categoryIds", $filter) && count($filter["categoryIds"]) > 0) {
            $categoryIdsCount = count($filter["categoryIds"]);
            $idx = 0;
            $categoryIdsINQuery = "(";
            foreach ($filter["categoryIds"] as $value) {
                $categoryIdsINQuery.=$value;
                $idx++;
                if ($idx != $categoryIdsCount) {
                    $categoryIdsINQuery.=",";
                }
            }
            $categoryIdsINQuery.=")";
            $relateProductIds = "( SELECT product_id FROM sa_product_n_category WHERE category_id IN " . $categoryIdsINQuery . " )";
            $condition .= ' AND p.id IN ' . $relateProductIds;
        }
        if (key_exists("languageCode", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.language_code = ?', $filter["languageCode"]);
        }
        if (key_exists("orderNumber", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.order_number = ?', $filter["orderNumber"]);
        }
        if (key_exists("relateOfProductId", $filter)) {
            $relateProductIds = "( SELECT related_product_id FROM sa_related_product WHERE "
                    . $this->getDbTable()->getAdapter()->quoteInto('product_id = ?', $filter["relateOfProductId"]) . " )";
            $condition .= ' AND ( p.id IN ' . $relateProductIds;
            $relatedProductIds = "( SELECT product_id FROM sa_related_product WHERE "
                    . $this->getDbTable()->getAdapter()->quoteInto('related_product_id = ?', $filter["relateOfProductId"]) . " )";
            $condition .= ' OR ( p.id IN ' . $relatedProductIds . " AND p.id NOT IN " . $relateProductIds . " ))";
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
        $order[] = 'p.order_number desc ';
        $order[] = 'p.create_time desc ';
        $select->order($order);
        //</editor-fold>
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "record-count" || $metric == "page-count") {
                $select = $this->getDbTable()->select()
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
        if ($metric == 'record-count') {
            //$temp = $select . " ";
            $row = $this->getDbTable()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'page-count') {
            $row = $this->getDbTable()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = $this->getDbTable()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $entry = $this->arrayToProduct($row);
                    //get images
                    $imageFilter = array();
                    $imageFilter['productId'] = $entry->getId();
                    $imageFilter['pageId'] = 0;
                    $imageFilter['pageSize'] = 0;
                    $images = $this->findProductImages($imageFilter);
                    $entry->setProductImages($images);
//                    //get consts
//                    $constFilter = array();
//                    $constFilter['productId'] = $entry->getId();
//                    $constFilter['pageId'] = 0;
//                    $constFilter['pageSize'] = 0;
//                    $consts = $this->findProductConsts($constFilter);
//                    $entry->setProductConsts($consts);
                    //
                    $retVal[] = $entry;
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @option
     *      keyword
     *      productName, productCode,
     *      categoryIds: array(id, type),     
     *      languageCode,
     *      productType,     
     *      orderNumber,    
     *      relateOfProductId,
     *      isNew,
     *      orders: array(
     *          array(
     *              'column' => ?,  (column in ["title", "create_time"])
     *              'type' => ?     (type in ["acs", "desc"])
     *          )
     *          ex: $filter['orders'] = array();
     *              $orderItem = array( 'column' => 'title' , 'type' => 'asc' );
     *              $filter['orders'][] = $orderItem;
     *      ),
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of PS_Entity_Product
     *      - case2: Interger
     */
    public function find1($filter) {
        $retVal = null;
        $select = $this->getDbTable()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";

        if (key_exists("isShow", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.is_show = ?', $filter["isShow"]);
        }
        if (key_exists("keyword", $filter)) {
            $condition .= ' AND ( ' . $this->getDbTable()->getAdapter()->quoteInto('p.name LIKE ?', '%' . $filter["keyword"] . '%')
                    . " OR " . $this->getDbTable()->getAdapter()->quoteInto('p.code LIKE ?', '%' . $filter["keyword"] . '%') . ' )';
        }
        if (key_exists("productName", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.name LIKE ?', '%' . $filter["productName"] . '%');
        }
        if (key_exists("productCode", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.code = ?', $filter["productCode"]);
        }
        if (key_exists("categoryIds", $filter) && count($filter["categoryIds"]) > 0) {
            $groupCategoryIds = array();

            foreach ($filter["categoryIds"] as $value) {
                if ($value["type"] != SA_Entity_ProductCategory::TYPE_GROUP) {
                    $relateProductIds = "( SELECT product_id FROM sa_product_n_category WHERE category_id = " . $value["id"] . " )";
                    $condition .= ' AND p.id IN ' . $relateProductIds;
                } else
                    $groupCategoryIds[] = $value["id"];
            }

            //Find Product with category type = GROUP
            if (count($groupCategoryIds) > 0) {
                $categoryIdsCount = count($groupCategoryIds);
                $idx = 0;
                $categoryIdsINQuery = "(";
                foreach ($groupCategoryIds as $value) {
                    $categoryIdsINQuery.=$value;
                    $idx++;
                    if ($idx != $categoryIdsCount) {
                        $categoryIdsINQuery.=",";
                    }
                }
                $categoryIdsINQuery.=")";
                $relateProductIds = "( SELECT product_id FROM sa_product_n_category WHERE category_id IN " . $categoryIdsINQuery . " )";
                $condition .= ' AND p.id IN ' . $relateProductIds;
            }
        }
        if (key_exists("languageCode", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.language_code = ?', $filter["languageCode"]);
        }
        if (key_exists("relateOfProductId", $filter)) {
            $relateProductIds = "( SELECT related_product_id FROM sa_related_product WHERE "
                    . $this->getDbTable()->getAdapter()->quoteInto('product_id = ?', $filter["relateOfProductId"]) . " )";
            $condition .= ' AND ( p.id IN ' . $relateProductIds;
            $relatedProductIds = "( SELECT product_id FROM sa_related_product WHERE "
                    . $this->getDbTable()->getAdapter()->quoteInto('related_product_id = ?', $filter["relateOfProductId"]) . " )";
            $condition .= ' OR ( p.id IN ' . $relatedProductIds . " AND p.id NOT IN " . $relateProductIds . " ))";
        }
        if (key_exists("isNew", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.status = ?', $filter["isNew"]);
        }
        if (key_exists("orderNumber", $filter)) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.order_number = ?', $filter["orderNumber"]);
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
        $order[] = 'p.order_number desc ';
        $order[] = 'p.create_time desc ';
        $select->order($order);
        //</editor-fold>
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "record-count" || $metric == "page-count") {
                $select = $this->getDbTable()->select()
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
        if ($metric == 'record-count') {
            //$temp = $select . " ";
            $row = $this->getDbTable()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'page-count') {
            $row = $this->getDbTable()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = $this->getDbTable()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $entry = $this->arrayToProduct($row);
                    //get images
                    $imageFilter = array();
                    $imageFilter['productId'] = $entry->getId();
                    $imageFilter['pageId'] = 0;
                    $imageFilter['pageSize'] = 0;
                    $images = $this->findProductImages($imageFilter);
                    $entry->setProductImages($images);
                    //get consts
                    $constFilter = array();
                    $constFilter['productId'] = $entry->getId();
                    $constFilter['pageId'] = 0;
                    $constFilter['pageSize'] = 0;
                    $consts = $this->findProductConsts($constFilter);
                    $entry->setProductConsts($consts);
                    //
                    $retVal[] = $entry;
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    public function assignProductToCategory($productId, $categoryId) {
        try {
            $this->getDbTableProductNCategory()->getAdapter()->beginTransaction();
            //insert product
            $data = array(
                "product_id" => $productId,
                "category_id" => $categoryId,
            );
            //insert
            $this->getDbTableProductNCategory()->insert($data);
            $retVal = $this->getDbTableProductNCategory()->getAdapter()->lastInsertId();
            if ($retVal <= 0) {
                throw new RuntimeException("Create Product N Category return id not valid !");
            }
            //
            $this->getDbTableProductNCategory()->getAdapter()->commit();
        } catch (Exception $ex) {
            $this->getDbTableProductNCategory()->getAdapter()->rollBack();
            throw $ex;
        }
    }

    public function unassignProductToCategory($productId, $categoryId) {
        $where = $this->getDbTableProductNCategory()->getAdapter()->quoteInto('product_id = ?', $productId);
        $where .= " AND " . $this->getDbTableProductNCategory()->getAdapter()->quoteInto('category_id = ?', $categoryId);
        $this->getDbTableProductNCategory()->delete($where);
    }

    public function unassignAllProductCategories($productId) {
        $where = $this->getDbTableProductNCategory()->getAdapter()->quoteInto('product_id = ?', $productId);
        $this->getDbTableProductNCategory()->delete($where);
    }

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
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
     *      productId,
     *      type, 
     *      languageCode,
     *      parentId,
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_ProductCategory
     *      - case2: Interger
     */
    public function findCategories($filter) {
        $retVal = null;
        $select = $this->getDbTableCategory()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product_category'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";
        if (key_exists("type", $filter)) {
            $condition .= ' AND ' . $this->getDbTableCategory()->getAdapter()->quoteInto('p.type = ?', $filter["type"]);
        }
        if (key_exists("languageCode", $filter)) {
            $condition .= ' AND ' . $this->getDbTableCategory()->getAdapter()->quoteInto('p.language_code = ?', $filter["languageCode"]);
        }
        if (key_exists("parentId", $filter)) {
            $condition .= ' AND ' . $this->getDbTableCategory()->getAdapter()->quoteInto('p.parent_id = ?', $filter["parentId"]);
        }
        if (key_exists("productId", $filter)) {
            $productConstIds = "( SELECT category_id FROM sa_product_n_category WHERE "
                    . $this->getDbTable()->getAdapter()->quoteInto('product_id = ?', $filter["productId"])
                    . " )";
            $condition .= ' AND p.id IN ' . $productConstIds;
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
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "record-count" || $metric == "page-count") {
                $select = $this->getDbTableCategory()->select()
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
        if ($metric == 'record-count') {
            //$temp = $select . " ";
            $row = $this->getDbTableCategory()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'page-count') {
            $row = $this->getDbTableCategory()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = $this->getDbTableCategory()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $entry = $this->arrayToProductCategory($row);
                    $retVal[] = $entry;
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    /**
     * 
     * @param type $id
     * @param type $languageCode
     * @return type $retVal - Array of Category;
     */
    public function getCategoryTreeForNavigation($id, $languageCode) {
        $retVal = array();
        $cate = $this->getCategory($id, $languageCode);

        while (isset($cate) && count($cate) > 0) {
            $temp = array("id" => $cate[0]->getId(), "name" => $cate[0]->getName());
            $retVal [] = $temp;
            $cate = $this->getCategory($cate[0]->getParentId(), $languageCode);
        }
        return $retVal;
    }

    /**
     * 
     * @param type $id
     * @return SA_Entity_Product
     */
    public function get($id, $languageCode) {
        $retVal = array();
        $select = $this->getDbTable()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product'));
        $condition = "1 = 1";
        $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.id = ?', $id);
        if (isset($languageCode) && $languageCode != NULL) {
            $condition .= ' AND ' . $this->getDbTable()->getAdapter()->quoteInto('p.language_code = ?', $languageCode);
        }
        $select->where($condition);
        $resultSet = $this->getDbTable()->fetchAll($select);
        foreach ($resultSet as $row) {
            if (isset($row->id) && $row->id != null) {
                $entity = $this->arrayToProduct($row);
                $retVal[] = $entity;
            }
        }
        return $retVal;
    }

    /**
     * 
     * @param type $categoryId
     * @return array of SA_Entity_ProductCategory
     */
    public function getCategory($categoryId, $languageCode) {
        $retVal = array();
        $select = $this->getDbTableCategory()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product_category'));
        $condition = "1 = 1";
        $condition .= ' AND ' . $this->getDbTableCategory()->getAdapter()->quoteInto('p.id = ?', $categoryId);
        if (isset($languageCode) && $languageCode != NULL) {
            $condition .= ' AND ' . $this->getDbTableCategory()->getAdapter()->quoteInto('p.language_code = ?', $languageCode);
        }
        $select->where($condition);
        $resultSet = $this->getDbTableCategory()->fetchAll($select);
        foreach ($resultSet as $row) {
            if (isset($row->id) && $row->id != null) {
                $entity = $this->arrayToProductCategory($row);
                $retVal[] = $entity;
            }
        }
        return $retVal;
    }

    /**
     * 
     * @param type $id
     * @return SA_Entity_ProductImage
     */
    public function getProductImage($id) {
        $where = $this->getDbTableProductImage()->getAdapter()->quoteInto("id = ?", $id);
        $row = $this->getDbTableProductImage()->fetchRow($where);
        $retVal = $this->arrayToProductImage($row);
        return $retVal;
    }

    /**
     * 
     * @param SA_Entity_Product $product
     */
    public function update(SA_Entity_Product $product) {
        $data = $this->productToArray($product);
        unset($data["id"]);
        unset($data["language_code"]);
        //update
        $this->getDbTable()->update($data, $this->getDbTable()->getAdapter()->quoteInto("id = ?", $product->getId()) . " AND " . $this->getDbTable()->getAdapter()->quoteInto('language_code = ?', $product->getLanguageCode()));
    }

    /**
     * 
     * @param SA_Entity_ProductCategory $category
     */
    public function updateCategory(SA_Entity_ProductCategory $category) {
        $data = $this->productCategoryToArray($category);
        unset($data["id"]);
        unset($data["language_code"]);
        //update
        $this->getDbTableCategory()->update($data, $this->getDbTableCategory()->getAdapter()->quoteInto("id = ?", $category->getId()) . " AND " . $this->getDbTableCategory()->getAdapter()->quoteInto('language_code = ?', $category->getLanguageCode()));
    }

    /**
     * 
     * @param type $id
     */
    public function downCategory($id) {
        $categories = Services::createProductService()->getCategory($id, NULL);
        foreach ($categories as $category) {
            if ($category->getType() != SA_Entity_ProductCategory:: TYPE_GROUP) {
                $silblingCategories = $this->findCategories(array(
                    "pageId" => "0",
                    "pageSize" => "0",
                    "languageCode" => $category->getLanguageCode(),
//                "parentId" => $category->getParentId(),
                    "type" => $category->getType(),
                        ));
            } else {
                $silblingCategories = $this->findCategories(array(
                    "pageId" => "0",
                    "pageSize" => "0",
                    "languageCode" => $category->getLanguageCode(),
                    "parentId" => $category->getParentId(),
                    "type" => $category->getType(),
                        ));
            }
            $categoryIdx = -1;
            foreach ($silblingCategories as $category) {
                $categoryIdx++;
                if ($category->getId() == $id) {
                    break;
                }
            }
            if ($categoryIdx < count($silblingCategories) - 1) {
                $targetCategory = $silblingCategories[$categoryIdx + 1];
                $this->swapCategorys($category, $targetCategory);
            }
        }
    }

    /**
     * 
     * @param type $id
     */
    public function upCategory($id) {
        $categories = Services::createProductService()->getCategory($id, NULL);
        foreach ($categories as $category) {
            $silblingCategories = array();

            if ($category->getType() != SA_Entity_ProductCategory:: TYPE_GROUP) {
                $silblingCategories = $this->findCategories(array(
                    "pageId" => "0",
                    "pageSize" => "0",
                    "languageCode" => $category->getLanguageCode(),
//                "parentId" => $category->getParentId(),
                    "type" => $category->getType(),
                        ));
            } else {
                $silblingCategories = $this->findCategories(array(
                    "pageId" => "0",
                    "pageSize" => "0",
                    "languageCode" => $category->getLanguageCode(),
                    "parentId" => $category->getParentId(),
                    "type" => $category->getType(),
                        ));
            }
            $categoryIdx = -1;
            foreach ($silblingCategories as $category) {
                $categoryIdx++;
                if ($category->getId() == $id) {
                    break;
                }
            }
            if ($categoryIdx > 0) {
                $targetCategory = $silblingCategories[$categoryIdx - 1];
                $this->swapCategorys($category, $targetCategory);
            }
        }
    }

    /**
     * 
     * @param type $productId
     * @param type $relatedProductId     
     */
    public function addRelation($productId, $relatedProductId) {
        if ($productId == null || $productId <= 0 ||
                $relatedProductId == null || $relatedProductId <= 0) {
            throw new RuntimeException("Uncondition for add  Product Relation !");
        }
        try {
            $this->getDbTableProductRelation()->getAdapter()->beginTransaction();
            //insert product
            $data = array(
                "product_id" => $productId,
                "related_product_id" => $relatedProductId,
                'create_time' => DateTimeUtil::toSqlString(new DateTime())
            );
            //insert
            $this->getDbTableProductRelation()->insert($data);
            $retVal = $this->getDbTableProductRelation()->getAdapter()->lastInsertId();
            if ($retVal <= 0) {
                throw new RuntimeException("Create Product Relation return id not valid !");
            }
            //
            $this->getDbTableProductRelation()->getAdapter()->commit();
        } catch (Exception $ex) {
            $this->getDbTableProductRelation()->getAdapter()->rollBack();
            throw $ex;
        }
    }

    /**
     * 
     * @param type $productId
     * @param type $relatedProductId
     */
    public function removeRelation($productId, $relatedProductId) {
        $where = "( " . $this->getDbTableProductRelation()->getAdapter()->quoteInto('product_id = ?', $productId);
        $where .= " AND " . $this->getDbTableProductRelation()->getAdapter()->quoteInto('related_product_id = ?', $relatedProductId) . " ) ";
        $where .= " OR ";
        $where = "( " . $this->getDbTableProductRelation()->getAdapter()->quoteInto('product_id = ?', $relatedProductId);
        $where .= " AND " . $this->getDbTableProductRelation()->getAdapter()->quoteInto('related_product_id = ?', $productId) . " ) ";
        $this->getDbTableProductRelation()->delete($where);
    }

    /**
     * 
     * @param type $productId     
     */
    public function removeAllRelation($productId) {
        $where = $this->getDbTableProductRelation()->getAdapter()->quoteInto('product_id = ?', $productId);
        $where .=' OR ' . $this->getDbTableProductRelation()->getAdapter()->quoteInto('related_product_id = ?', $productId);
        $this->getDbTableProductRelation()->delete($where);
    }

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @options
     *      productId, 
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_ProductImage
     *      - case2: Interger
     */
    public function findProductImages($filter) {
        $retVal = null;
        $select = $this->getDbTableProductImage()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product_image'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";
        if (key_exists("productId", $filter)) {
            $condition .= ' AND ' . $this->getDbTableProductImage()->getAdapter()->quoteInto('p.product_id = ?', $filter["productId"]);
        }
        //</editor-fold>
        $select->where($condition);
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "record-count" || $metric == "page-count") {
                $select = $this->getDbTableProductImage()->select()
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
        if ($metric == 'record-count') {
            //$temp = $select . " ";
            $row = $this->getDbTableProductImage()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'page-count') {
            $row = $this->getDbTableProductImage()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = $this->getDbTableProductImage()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $entry = $this->arrayToProductImage($row);
                    $retVal[] = $entry;
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    /**
     * 
     * @param SA_Entity_ProductImage $productImage
     * @return id of new product image     
     */
    public function createProductImage(SA_Entity_ProductImage $productImage) {
        if ($productImage->getProductId() == null || $productImage->getProductId() <= 0 ||
                $productImage->getUrl() == null || $productImage->getCreateTime() == null) {
            throw new RuntimeException("Uncondition for create Product Image !");
        }
        try {
            $this->getDbTableProductImage()->getAdapter()->beginTransaction();
            //insert product
            $data = $this->productImageToArray($productImage);
            unset($data['id']);
            //insert
            $this->getDbTableProductImage()->insert($data);
            $retVal = $this->getDbTableProductImage()->getAdapter()->lastInsertId();
            if ($retVal <= 0) {
                throw new RuntimeException("Create Product Image return id not valid !");
            }
            //
            $this->getDbTableProductImage()->getAdapter()->commit();
        } catch (Exception $ex) {
            $this->getDbTableProductImage()->getAdapter()->rollBack();
            throw $ex;
        }
        return $retVal;
    }
    
    public function updateProductImage(SA_Entity_ProductImage $productImage) {
        $data = $this->productImageToArray($productImage);
        unset($data["id"]);
        //update
        $this->getDbTableProductImage()->update($data, $this->getDbTableProductImage()->getAdapter()->quoteInto("id = ?", $productImage->getId()));
    }

    /**
     * 
     * @param type $id     
     */
    public function deleteProductImage($id) {
        $productImage = $this->getProductImage($id);
        if ($productImage != NULL) {
            $this->getDbTableProductImage()
                    ->delete($this->getDbTableProductImage()->getAdapter()->quoteInto('id = ?', $productImage->getId()));
            //delete file
            try {
                if (file_exists($productImage->getUrl())) {
                    unlink($productImage->getUrl());
                }
            } catch (Exception $exc) {
                throw new RuntimeException("Cannot delete file: " . $productImage->getUrl());
            }
        } else {
            throw new RuntimeException("Not exist!");
        }
    }

    /**
     * 
     * @param type $productId     
     */
    public function deleteProductImageByProductId($productId) {
        //get list of product image
        $productImages = $this->findProductImages($productId);
        $this->getDbTableProductImage()
                ->delete($this->getDbTableProductImage()->getAdapter()->quoteInto('product_id = ?', $productId));
        //delete file
        $deleteFileError = "";
        foreach ($productImages as $productImage) {
            /* @var $productImage SA_Entity_ProductImage */
            try {
                if (file_exists($productImage->getUrl())) {
                    unlink($productImage->getUrl());
                }
            } catch (Exception $exc) {
                $deleteFileError .= "Cannot delete file: " . $productImage->getUrl() + "\n";
            }
        }
        if ($deleteFileError != "") {
            throw new RuntimeException($deleteFileError);
        }
    }

    /**
     * 
     * @param array filter
     * @require
     *      - case1: metric = null OR not exist (require: pageId, pageSize)
     *      - case2: metric != null 
     *              + metric = "record-count" (require: not exist pageId & pageSize)
     *              + metric = "page-count" (require: pageSize)
     * @options
     *      productId, 
     *      type,
     *      pageId, pageSize (pageId = pageSize = 0 <=> get all)
     * @return 
     *      - case1: array of SA_Entity_ProductConst
     *      - case2: Interger
     */
    public function findProductConsts($filter) {
        $retVal = null;
        $select = $this->getDbTableProductImage()->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'sa_product_const'));
        //<editor-fold desc="build condition in where statement" defaultstate="collapsed">
        $condition = "1 = 1";
        if (key_exists("type", $filter)) {
            $condition .= ' AND ' . $this->getDbTableProductConst()->getAdapter()->quoteInto('p.type = ?', $filter["type"]);
        }
        if (key_exists("productId", $filter)) {
            $productConstIds = "( SELECT product_const_id FROM sa_product_n_const WHERE "
                    . $this->getDbTableProductConst()->getAdapter()->quoteInto('product_id = ?', $filter["productId"])
                    . " )";
            $condition .= ' AND p.id IN ' . $productConstIds;
        }
        //</editor-fold>
        $select->where($condition);
        //<editor-fold desc="metric or paging" defaultstate="collapsed">
        $metric = null;
        if (key_exists("metric", $filter)) {
            $metric = $filter['metric'];
            if ($metric == "record-count" || $metric == "page-count") {
                $select = $this->getDbTableProductConst()->select()
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
        if ($metric == 'record-count') {
            //$temp = $select . " ";
            $row = $this->getDbTableProductConst()->fetchRow($select);
            $retVal = (int) $row->COUNT;
        } elseif ($metric == 'page-count') {
            $row = $this->getDbTableProductConst()->fetchRow($select);
            $retVal = Util::recordsCountToPagesCount($row->COUNT, $filter['pageSize']);
        } else {
            $retVal = array();
            $resultSet = $this->getDbTableProductConst()->fetchAll($select);
            foreach ($resultSet as $row) {
                if (isset($row->id) && $row->id != null) {
                    $entry = $this->arrayToProductConst($row);
                    $retVal[] = $entry;
                }
            }
        }
        //</editor-fold> 
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Utils
    private function swapCategorys(SA_Entity_ProductCategory $category1, SA_Entity_ProductCategory $category2) {
        if ($category1->getLanguageCode() != $category2->getLanguageCode() || $category1->getType() != $category2->getType()) {
            return;
        }
        //create where condition
        $where1 = $this->getDbTableCategory()->getAdapter()->quoteInto("id = ?", $category1->getId())
                . " AND " . $this->getDbTableCategory()->getAdapter()->quoteInto("order_number = ?", $category1->getOrderNumber())
                . " AND " . $this->getDbTableCategory()->getAdapter()->quoteInto("language_code = ?", $category1->getLanguageCode());
        $where2 = $this->getDbTableCategory()->getAdapter()->quoteInto("id = ?", $category2->getId())
                . " AND " . $this->getDbTableCategory()->getAdapter()->quoteInto("order_number = ?", $category2->getOrderNumber())
                . " AND " . $this->getDbTableCategory()->getAdapter()->quoteInto("language_code = ?", $category2->getLanguageCode());
        //update
        $this->getDbTableCategory()->update(array("order_number" => $category2->getOrderNumber()), $where1);
        $this->getDbTableCategory()->update(array("order_number" => $category1->getOrderNumber()), $where2);
    }

    /**
     * 
     * @param type $row
     * @return SA_Entity_ProductCategory
     */
    private function arrayToProductCategory($row) {
        $retVal = new SA_Entity_ProductCategory();
        $retVal->setId($row->id)
                ->setName($row->name)
                ->setOrderNumber($row->order_number)
                ->setDescription($row->description)
                ->setLanguageCode($row->language_code)
                ->setParentId($row->parent_id)
                ->setThumbnailUrl($row->thumbnail_url)
                ->setType($row->type)
                ->setCreateTime(DateTimeUtil::parseSqlString($row->create_time))
                ->setUpdateTime(DateTimeUtil::parseSqlString($row->update_time));
        return $retVal;
    }

    /**
     * 
     * @param type $row
     * @return SA_Entity_Product
     */
    private function arrayToProduct($row) {
        $retVal = new SA_Entity_Product();
        $retVal->setId($row->id)
                ->setCode($row->code)
                ->setName($row->name)
                ->setListPrice($row->list_price)
                ->setSalePrice($row->sale_price)
                ->setCurrencyCode($row->currency_code)
                ->setCategoryId($row->category_id)
                ->setStatus($row->status)
                ->setQuantity($row->quantity)
                ->setIsShow($row->is_show)
                ->setCreatorId($row->creator_id)
                ->setDescription($row->description)
                ->setCreateTime(DateTimeUtil::parseSqlString($row->create_time))
                ->setUpdateTime(DateTimeUtil::parseSqlString($row->update_time))
                ->setOrigin($row->origin)
                ->setThumbnailUrl($row->thumbnail_url)
                ->setSize($row->size)
                ->setLanguageCode($row->language_code)
                ->setComment($row->comment)
                ->setEvaluation($row->evaluation)
                ->setFeature($row->feature)
                ->setTechnique($row->technique)
                ->setModel($row->model)
                ->setOrderNumber($row->order_number)
                ->setIsNew($row->is_new);
        return $retVal;
    }

    private function productToArray(SA_Entity_Product $obj) {
        return array(
            'id' => $obj->getId(),
            'code' => $obj->getCode(),
            'name' => $obj->getName(),
            'list_price' => $obj->getListPrice(),
            'sale_price' => $obj->getSalePrice(),
            'currency_code' => $obj->getCurrencyCode(),
            'category_id' => $obj->getCategoryId(),
            'status' => $obj->getStatus(),
            'quantity' => $obj->getQuantity(),
            'is_show' => $obj->getIsShow(),
            'creator_id' => $obj->getCreatorId(),
            'description' => $obj->getDescription(),
            'create_time' => DateTimeUtil::toSqlString($obj->getCreateTime()),
            'update_time' => DateTimeUtil::toSqlString($obj->getUpdateTime()),
            'origin' => $obj->getOrigin(),
            'thumbnail_url' => $obj->getThumbnailUrl(),
            'size' => $obj->getSize(),
            'language_code' => $obj->getLanguageCode(),
            'comment' => $obj->getComment(),
            'evaluation' => $obj->getEvaluation(),
            'feature' => $obj->getFeature(),
            'technique' => $obj->getTechnique(),
            'model' => $obj->getModel(),
            'order_number' => $obj->getOrderNumber(),
            'is_new' => $obj->getIsNew()
        );
    }

    private function productCategoryToArray(SA_Entity_ProductCategory $obj) {
        return array(
            'id' => $obj->getId(),
            'name' => $obj->getName(),
            'description' => $obj->getDescription(),
            'order_number' => $obj->getOrderNumber(),
            'language_code' => $obj->getLanguageCode(),
            'parent_id' => $obj->getParentId(),
            'thumbnail_url' => $obj->getThumbnailUrl(),
            'type' => $obj->getType(),
            'create_time' => DateTimeUtil::toSqlString($obj->getCreateTime()),
            'update_time' => DateTimeUtil::toSqlString($obj->getUpdateTime())
        );
    }

    private function productImageToArray(SA_Entity_ProductImage $obj) {
        return array(
            'id' => $obj->getId(),
            'product_id' => $obj->getProductId(),
            'name' => $obj->getName(),
            'url' => $obj->getUrl(),
            'thumbnail_url' => $obj->getThumbnailUrl(),
            'is_representation' => $obj->getIsRepresentation(),
            'create_time' => DateTimeUtil::toSqlString($obj->getCreateTime())
        );
    }

    /**
     * 
     * @param type $row
     * @return SA_Entity_ProductImage
     */
    private function arrayToProductImage($row) {
        $retVal = new SA_Entity_ProductImage();
        $retVal->setId($row->id)
                ->setProductId($row->product_id)
                ->setName($row->name)
                ->setUrl($row->url)
                ->setThumbnailUrl($row->thumbnail_url)
                ->setIsRepresentation($row->is_representation)
                ->setCreateTime(DateTimeUtil::parseSqlString($row->create_time));
        return $retVal;
    }

    /**
     * 
     * @param type $row
     * @return SA_Entity_ProductConst
     */
    private function arrayToProductConst($row) {
        $retVal = new SA_Entity_ProductConst();
        $retVal->setId($row->id)
                ->setCode($row->code)
                ->setName($row->name)
                ->setType($row->type)
                ->setDescription($row->description)
                ->setCreateTime(DateTimeUtil::parseSqlString($row->create_time))
                ->setUpdateTime(DateTimeUtil::parseSqlString($row->update_time));
        return $retVal;
    }

    //--------------------------------------------------------------------------    
//  Inner class
}

?>
