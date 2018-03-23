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
class SA_WorkshopMapper implements SA_WorkshopService {

    public function create(SA_Entity_Workshop $workshop) {
        $data = MapperUtil::mapObject($workshop, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data['id']);
        MapperUtil::getDbTable_Workshop()->insert($data);
        $retVal = MapperUtil::getDbTable_Workshop()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete($id) {
        MapperUtil::getDbTable_Workshop()
                ->delete(MapperUtil::getDbTable_Workshop()->getAdapter()->quoteInto("id = ?", $id));
    }

    public function find($filter) {
        $retVal = array();
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Workshop()->select()
                    ->from(array("s" => "sa_workshop"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Workshop()->select()
                    ->from(array("s" => "sa_workshop"));
        }
        $condition = "1=1 ";
        if (key_exists("top", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Workshop()->getAdapter()->quoteInto("s.is_top = ?", $filter["top"]);
        }

        if (key_exists("title", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Workshop()->getAdapter()->quoteInto("s.title LIKE ?", "'%" . $filter["title"] . "%'");
        }
        if (key_exists("createTimeFrom", $filter) && $filter["createTimeFrom"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.create_time >= ?", $filter["createTimeFrom"]);
        }
        if (key_exists("createTimeTo", $filter) && $filter["createTimeTo"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.create_time <= ?", $filter["createTimeTo"]);
        }

        $select->where($condition)
                ->order('create_time desc');
//        var_dump($select->assemble());
        if (key_exists("metric", $filter)) {
            $row = MapperUtil::getDbTable_Workshop()->fetchRow($select);
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
            $resultSet = MapperUtil::getDbTable_Workshop()->fetchAll($select);
            foreach ($resultSet as $row) {
                $workshop = MapperUtil::toObject("SA_Entity_Workshop", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $workshop;
            }
        }
        return $retVal;
    }

    public function get($id) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Workshop()->fetchRow(MapperUtil::getDbTable_Workshop()->getAdapter()->quoteInto("id = ?", $id));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Workshop", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function update(SA_Entity_Workshop $workshop) {
        $data = MapperUtil::mapObject($workshop, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        //update
        MapperUtil::getDbTable_Workshop()->update($data, MapperUtil::getDbTable_Workshop()->getAdapter()->quoteInto("id = ?", $workshop->getId()));
    }

}
