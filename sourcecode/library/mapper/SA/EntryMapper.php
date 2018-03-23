<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EntryMapper
 *
 * @author ThoMC
 */
class SA_EntryMapper implements SA_EntryService {

    public function create(SA_Entity_Entry $entry) {
        $data = MapperUtil::mapObject($entry, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data['id']);
        MapperUtil::getDbTable_Entry()->insert($data);
        $retVal = MapperUtil::getDbTable_Entry()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete($id) {
        //delete related entry           
        $where = MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('entry_id = ?', $id);
        $where .=' OR ' . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('related_entry_id = ?', $id);
        MapperUtil::getDbTable_RelatedEntry()
                ->delete($where);

        MapperUtil::getDbTable_Entry()
                ->delete(MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("id = ?", $id));
    }

    public function find($filter) {
        $retVal = array();
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Entry()->select()
                    ->from(array("s" => "sa_entry"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Entry()->select()
                    ->from(array("s" => "sa_entry"));
        }
        $condition = "1=1 ";
        if (key_exists("type", $filter)) {
            $types = $filter["type"];
            $condition .= " AND s.category_id IN (SELECT id FROM sa_category WHERE type IN (";
            foreach ($types as $t)
                $condition .= " \"" . $t . "\", ";
            $condition .= " \"\")) ";
        }
        if (key_exists("top", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.is_top = ?", $filter["top"]);
        }
        if (key_exists("display", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.display = ?", $filter["display"]);
        }
        if (key_exists("categoryId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.category_id = ?", $filter["categoryId"]);
        }
        if (key_exists("parentCategoryId", $filter)) {
            $condition .= " AND (" . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.category_id IN ( SELECT id FROM sa_category WHERE parent_id = ? ) ", $filter["parentCategoryId"]);
            $condition .= " OR " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.category_id = ?", $filter["parentCategoryId"]) . ")";
        }
        if (key_exists("categoryType", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.category_id IN ( SELECT id FROM sa_category WHERE type = ? ) ", $filter["categoryType"]);
        }
        if (key_exists("creatorId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.creator_id = ?", $filter["creatorId"]);
        }
        if (key_exists("title", $filter) && $filter["title"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.title LIKE ?", "%" . $filter["title"] . "%");
        }
        if (key_exists("asker", $filter) && $filter["asker"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.asker LIKE ?", "%" . $filter["asker"] . "%");
        }
        if (key_exists("askerEmail", $filter) && $filter["askerEmail"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.asker_email LIKE ?", "%" . $filter["askerEmail"] . "%");
        }
        if (key_exists("keyword", $filter) && $filter["keyword"] != "") {
            $condition .= " AND ( "
                    . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.description LIKE ?", "%" . $filter["keyword"] . "%")
                    . " OR " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.tags like ?", "%" . $filter["keyword"] . "%")
                    . " OR " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.title LIKE ?", "%" . $filter["keyword"] . "%") . " ) ";
        }
        if (key_exists("createTimeFrom", $filter) && $filter["createTimeFrom"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.create_time >= ?", $filter["createTimeFrom"]);
        }
        if (key_exists("createTimeTo", $filter) && $filter["createTimeTo"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.create_time <= ?", $filter["createTimeTo"]);
        }

        if (key_exists("olderTimeTo", $filter) && $filter["olderTimeTo"] != "") {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.create_time < ?", $filter["olderTimeTo"]);
        }

        if (key_exists("olderEntryId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.create_time <> ?", $filter["ignoreCategoryId"]);
        }

        if (key_exists("ignoreEntryIds", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("s.id NOT IN(?)", $filter["ignoreEntryIds"]);
        }

        if (key_exists("relateOfEntryId", $filter)) {
            $relateEntryIds = "( SELECT related_entry_id FROM sa_related_entry WHERE "
                    . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('entry_id = ?', $filter["relateOfEntryId"]) . " )";
            $condition .= ' AND ( s.id IN ' . $relateEntryIds;
            $relatedEntryIds = "( SELECT entry_id FROM sa_related_entry WHERE "
                    . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('related_entry_id = ?', $filter["relateOfEntryId"]) . " )";
            $condition .= ' OR ( s.id IN ' . $relatedEntryIds . " AND s.id NOT IN " . $relateEntryIds . " ))";
        }

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
            $row = MapperUtil::getDbTable_Entry()->fetchRow($select);
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
            $resultSet = MapperUtil::getDbTable_Entry()->fetchAll($select);
            foreach ($resultSet as $row) {
                $entry = MapperUtil::toObject("SA_Entity_Entry", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $entry;
            }
        }
        return $retVal;
    }

    public function get($id) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Entry()->fetchRow(MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("id = ?", $id));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Entry", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function getByUrl($url) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Entry()->fetchRow(MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("rewrite_url = ?", $url));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Entry", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function update(SA_Entity_Entry $entry) {
        $data = MapperUtil::mapObject($entry, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        //update
        MapperUtil::getDbTable_Entry()->update($data, MapperUtil::getDbTable_Entry()->getAdapter()->quoteInto("id = ?", $entry->getId()));
    }

    public function getMaxOrderNumber() {
        $select = MapperUtil::getDbTable_Entry()->select()
                ->from(array('e' => 'sa_entry'), array(new Zend_Db_Expr("MAX(order_number) AS maxOrder")));

        $resultSet = MapperUtil::getDbTable_Entry()->fetchAll($select);
        if (isset($resultSet) && count($resultSet) > 0) {
            return $resultSet[0]['maxOrder'];
        }
        return '0';
    }

    public function addRelation($entryId, $relatedEntryId) {
        if ($entryId == null || $entryId <= 0 ||
                $relatedEntryId == null || $relatedEntryId <= 0) {
            throw new RuntimeException("Uncondition for add  Entry Relation !");
        }
        try {
            MapperUtil::getDbTable_RelatedEntry()->getAdapter()->beginTransaction();
            //insert product
            $data = array(
                "entry_id" => $entryId,
                "related_entry_id" => $relatedEntryId,
                'create_time' => DateTimeUtil::toSqlString(new DateTime())
            );
            //insert
            MapperUtil::getDbTable_RelatedEntry()->insert($data);
            $retVal = MapperUtil::getDbTable_RelatedEntry()->getAdapter()->lastInsertId();
            if ($retVal <= 0) {
                throw new RuntimeException("Create Entry Relation return id not valid !");
            }
            //
            MapperUtil::getDbTable_RelatedEntry()->getAdapter()->commit();
        } catch (Exception $ex) {
            MapperUtil::getDbTable_RelatedEntry()->getAdapter()->rollBack();
            throw $ex;
        }
    }

    public function removeRelation($entryId, $relatedEntryId) {
        $where = "( " . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('entry_id = ?', $entryId);
        $where .= " AND " . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('related_entry_id = ?', $relatedEntryId) . " ) ";
        $where .= " OR ";
        $where = "( " . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('entry_id = ?', $relatedEntryId);
        $where .= " AND " . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('related_entry_id = ?', $entryId) . " ) ";
        MapperUtil::getDbTable_RelatedEntry()->delete($where);
    }

    public function removeAllRelation($entryId) {
        $where = MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('entry_id = ?', $entryId);
        $where .=' OR ' . MapperUtil::getDbTable_RelatedEntry()->getAdapter()->quoteInto('related_entry_id = ?', $entryId);
        MapperUtil::getDbTable_RelatedEntry()->delete($where);
    }

}
