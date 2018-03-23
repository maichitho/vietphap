<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotificationMapper
 *
 * @author ThoMC
 */
class SA_NotificationMapper implements SA_NotificationService {

    public function create(SA_Entity_Notification $notification) {
        $data = MapperUtil::mapObject($notification, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data['id']);
        MapperUtil::getDbTable_Notification()->insert($data);
        $retVal = MapperUtil::getDbTable_Notification()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete($id) {
        MapperUtil::getDbTable_Notification()
                ->delete(MapperUtil::getDbTable_Notification()->getAdapter()->quoteInto("id = ?", $id));
    }

    public function find($filter) {
        $retVal = array();
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Notification()->select()
                    ->from(array("s" => "sa_notification"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Notification()->select()
                    ->from(array("s" => "sa_notification"));
        }
        $condition = "1=1 ";
        if (key_exists("type", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Notification()->getAdapter()->quoteInto("s.type = ?", $filter["type"]);
        }
        if (key_exists("new", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Notification()->getAdapter()->quoteInto("s.new = ?", $filter["new"]);
        }
        $select->where($condition)
                ->order('time desc');
        if (key_exists("metric", $filter)) {
            $row = MapperUtil::getDbTable_Notification()->fetchRow($select);
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
            $resultSet = MapperUtil::getDbTable_Notification()->fetchAll($select);
            foreach ($resultSet as $row) {
                $notification = MapperUtil::toObject("SA_Entity_Notification", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $notification;
            }
        }
        return $retVal;
    }

    public function get($id) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Notification()->fetchRow(MapperUtil::getDbTable_Notification()->getAdapter()->quoteInto("id = ?", $id));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Notification", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function getNewCountByType() {
        $retVal = array();
        $configService = Services::createConfigurationService();

        $retVal[] = array('type' => SA_Entity_Notification::TYPE_FEEDBACK,
            'newCount' => $this->find(array('metric' => 'recordCount',
                'type' => SA_Entity_Notification::TYPE_FEEDBACK,
                'new' => 1)),
            'list' => ControllerUtils::mapNotifications($this->find(array('pageId' => 0,
                        'pageSize' => $configService->get('feedback.system.page.size')->getValue(),
                        'type' => SA_Entity_Notification::TYPE_FEEDBACK))));

        $retVal[] = array('type' => SA_Entity_Notification::TYPE_SUPPLIER_REGISTER,
            'newCount' => $this->find(array('metric' => 'recordCount',
                'type' => SA_Entity_Notification::TYPE_SUPPLIER_REGISTER,
                'new' => 1)),
            'list' => ControllerUtils::mapNotifications($this->find(array('pageId' => 0,
                        'pageSize' => $configService->get('feedback.system.page.size')->getValue(),
                        'type' => SA_Entity_Notification::TYPE_SUPPLIER_REGISTER))));

        $retVal[] = array('type' => SA_Entity_Notification::TYPE_NEW_ORDER,
            'newCount' => $this->find(array('metric' => 'recordCount',
                'type' => SA_Entity_Notification::TYPE_NEW_ORDER,
                'new' => 1)),
            'list' => ControllerUtils::mapNotifications($this->find(array('pageId' => 0,
                        'pageSize' => $configService->get('feedback.system.page.size')->getValue(),
                        'type' => SA_Entity_Notification::TYPE_NEW_ORDER))));

        $retVal[] = array('type' => SA_Entity_Notification::TYPE_CUSTOMER_REGISTER,
            'newCount' => $this->find(array('metric' => 'recordCount',
                'type' => SA_Entity_Notification::TYPE_CUSTOMER_REGISTER,
                'new' => 1)),
            'list' => ControllerUtils::mapNotifications($this->find(array('pageId' => 0,
                        'pageSize' => $configService->get('feedback.system.page.size')->getValue(),
                        'type' => SA_Entity_Notification::TYPE_CUSTOMER_REGISTER))));

        return $retVal;
    }

    public function update(SA_Entity_Notification $notification) {
        $data = MapperUtil::mapObject($notification, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        //update
        MapperUtil::getDbTable_Notification()->update($data, MapperUtil::getDbTable_Notification()->getAdapter()->quoteInto("id = ?", $notification->getId()));
    }

    public function updateAll($type) {
        try {
            MapperUtil::getDbTable_Notification()->getAdapter()->beginTransaction();
            $data = array('new' => '0');
            $where = "new = 1";
            $where.= " AND " . MapperUtil::getDbTable_Notification()->getAdapter()->quoteInto("type = ?", $type);
            MapperUtil::getDbTable_Notification()->update($data, $where);

            MapperUtil::getDbTable_Notification()->getAdapter()->commit();
        } catch (Exception $e) {
            MapperUtil::getDbTable_Notification()->getAdapter()->rollBack();
            throw $e;
        }
    }

}
