<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DrugstoreMapper
 *
 * @author ThoMC
 */
class SA_DrugstoreMapper implements SA_DrugstoreService {

    public function create(SA_Entity_Drugstore $drugstore) {
        $data = MapperUtil::mapObject($drugstore, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data['id']);
        unset($data['ui_city_name']);
        unset($data['ui_district_name']);
        MapperUtil::getDbTable_Drugstore()->insert($data);
        $retVal = MapperUtil::getDbTable_Drugstore()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete($id) {
        MapperUtil::getDbTable_Drugstore()
                ->delete(MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("id = ?", $id));
    }

    public function deleteByLocation($idCity, $idDis) {
        $sql = "1=1 ";
        $delete = false;
        if ($idCity != null) {
            $delete = true;
            $sql = $sql . " AND " . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("city_id = ?", $idCity);
        }
        if ($idDis != null) {
            $delete = true;
            $sql = $sql. " AND "  . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("district_id = ?", $idDis);
        }
        MapperUtil::getDbTable_Drugstore()
                ->delete($sql);
    }

    public function find($filter) {
        $retVal = array();
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Drugstore()->select()
                    ->from(array("s" => "sa_drugstore"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Drugstore()->select()
                    ->setIntegrityCheck(false)
                    ->from(array("s" => "sa_drugstore"))
                    ->joinLeft(array("c" => "sa_location"), 's.city_id = c.id', array('ui_city_name' => 'c.name'))
                    ->joinLeft(array("d" => "sa_location"), 's.district_id = d.id', array('ui_district_name' => 'd.name'));
        }
        $condition = "1=1 ";

        if (key_exists("status", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("s.status = ?", $filter["status"]);
        }
        if (key_exists("cityId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("s.city_id = ?", $filter["cityId"]);
        }
        if (key_exists("districtId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("s.district_id = ?", $filter["districtId"]);
        }
        if (key_exists("name", $filter) && $filter["name"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("s.name LIKE ?", "%" . $filter["name"] . "%");
        }
//        if (key_exists("name", $filter) && $filter["name"] != "") {
//            $condition .= " AND " . MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("s.name LIKE ?", "%" . $filter["name"] . "%");
//        }

        $select->where($condition);
        //<editor-fold desc="order" defaultstate="collapsed">
        $order = array();
        if (key_exists("orders", $filter)) {
            foreach ($filter['orders'] as $orderItem) {
                $order[] = "s." . $orderItem['column'] . " " . $orderItem['type'];
            }
        }
        $order[] = 's.create_time DESC ';
        $select->order($order);
        //</editor-fold>
//        var_dump($select->assemble());
        if (key_exists("metric", $filter)) {
            $row = MapperUtil::getDbTable_Drugstore()->fetchRow($select);
            $recordsCount = $row->COUNT;
            if ($filter["metric"] == "pageCount") {
                $retVal = Util::recordsCountToPagesCount($recordsCount, $filter["pageSize"]);
            } else {
                $retVal = $recordsCount;
            }
        } else {
            if (isset($filter["pageId"]) && isset($filter["pageSize"]) && ($filter["pageId"] != 0 || $filter["pageSize"] != 0 )) {
                $select->limitPage($filter["pageId"] + 1, $filter["pageSize"]);
            }
            $resultSet = MapperUtil::getDbTable_Drugstore()->fetchAll($select);
            foreach ($resultSet as $row) {
                $drugstore = MapperUtil::toObject("SA_Entity_Drugstore", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $drugstore;
            }
        }
        return $retVal;
    }

    public function get($id) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Drugstore()->fetchRow(MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("id = ?", $id));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Drugstore", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function update(SA_Entity_Drugstore $drugstore) {
        $data = MapperUtil::mapObject($drugstore, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        unset($data['ui_city_name']);
        unset($data['ui_district_name']);
        //update
        MapperUtil::getDbTable_Drugstore()->update($data, MapperUtil::getDbTable_Drugstore()->getAdapter()->quoteInto("id = ?", $drugstore->getId()));
    }

    public function getMaxOrderNumber() {
        $select = MapperUtil::getDbTable_Drugstore()->select()
                ->from(array('d' => 'sa_drugstore'), array(new Zend_Db_Expr("MAX(order_number) AS maxOrder")));

        $resultSet = MapperUtil::getDbTable_Drugstore()->fetchAll($select);
        if (isset($resultSet) && count($resultSet) > 0) {
            return $resultSet[0]['maxOrder'];
        }
        return '0';
    }

}
