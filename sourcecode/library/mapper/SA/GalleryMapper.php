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
class SA_GalleryMapper implements SA_GalleryService {

    public function create(SA_Entity_Gallery $gallery) {
        try {
            MapperUtil::getDbTable_Gallery()->getAdapter()->beginTransaction();
            //insert gallery
            $data = MapperUtil::mapObject($gallery, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data['id']);
            unset($data['images']);
            MapperUtil::getDbTable_Gallery()->insert($data);
            //created id
            $retVal = MapperUtil::getDbTable_Gallery()->getAdapter()->lastInsertId();

            if ($retVal <= 0) {
                throw new RuntimeException("Create Gallery return id not valid !");
            }
            //
            MapperUtil::getDbTable_Gallery()->getAdapter()->commit();
        } catch (Exception $ex) {
            MapperUtil::getDbTable_Gallery()->getAdapter()->rollBack();
            throw $ex;
        }
        return $retVal;
    }

    public function createImage(SA_Entity_Image $image) {
        try {
            MapperUtil::getDbTable_Image()->getAdapter()->beginTransaction();
            //insert gallery item
            $data = MapperUtil::mapObject($image, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
            unset($data['id']);

            MapperUtil::getDbTable_Image()->insert($data);
            //created id
            $retVal = MapperUtil::getDbTable_Image()->getAdapter()->lastInsertId();
            if ($retVal <= 0) {
                throw new RuntimeException("Create Image return id not valid !");
            }
            //
            MapperUtil::getDbTable_Image()->getAdapter()->commit();
        } catch (Exception $ex) {
            MapperUtil::getDbTable_Image()->getAdapter()->rollBack();
            throw $ex;
        }
        return $retVal;
    }

    public function delete($id) {
        MapperUtil::getDbTable_Image()
                ->delete(MapperUtil::getDbTable_Image()->getAdapter()->quoteInto("gallery_id = ?", $id));
        MapperUtil::getDbTable_Gallery()
                ->delete(MapperUtil::getDbTable_Gallery()->getAdapter()->quoteInto("id = ?", $id));
    }

    public function deleteImage($id) {
        MapperUtil::getDbTable_Image()
                ->delete(MapperUtil::getDbTable_Image()->getAdapter()->quoteInto("id = ?", $id));
    }

    public function find($filter) {
        $retVal = array();
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Gallery()->select()
                    ->from(array("s" => "sa_gallery"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Gallery()->select()
                    ->from(array("s" => "sa_gallery"));
        }
        $condition = "1=1 ";
        if (key_exists("top", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Gallery()->getAdapter()->quoteInto("s.is_top = ?", $filter["top"]);
        }

        if (key_exists("title", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Gallery()->getAdapter()->quoteInto("s.title LIKE ?", "%" . $filter["title"] . "%");
        }

        $select->where($condition)
                ->order('create_time desc');
        if (key_exists("metric", $filter)) {
            $row = MapperUtil::getDbTable_Gallery()->fetchRow($select);
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
            $resultSet = MapperUtil::getDbTable_Gallery()->fetchAll($select);
            foreach ($resultSet as $row) {
                $gallery = MapperUtil::toObject("SA_Entity_Gallery", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $gallery;
            }
        }
        return $retVal;
    }

    public function findImage($filter) {
        $retVal = array();
        if (key_exists("metric", $filter)) {
            $select = MapperUtil::getDbTable_Image()->select()
                    ->from(array("s" => "sa_image"), array("COUNT" => "COUNT(*)"));
        } else {
            $select = MapperUtil::getDbTable_Image()->select()
                    ->setIntegrityCheck(false)
                    ->from(array("s" => "sa_image"));
        }
        $condition = "1=1 ";
        if (key_exists("galleryId", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Image()->getAdapter()->quoteInto("s.gallery_id = ?", $filter["galleryId"]);
        }
        if (key_exists("galleryIds", $filter)) {
            $condition .= " AND " . MapperUtil::getDbTable_Image()->getAdapter()->quoteInto("s.gallery_id IN (?)", $filter["galleryIds"]);
        }

        $select->where($condition)
                ->order('create_time desc');
        if (key_exists("metric", $filter)) {
            $row = MapperUtil::getDbTable_Image()->fetchRow($select);
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
            $resultSet = MapperUtil::getDbTable_Image()->fetchAll($select);
            foreach ($resultSet as $row) {
                $entry = MapperUtil::toObject("SA_Entity_Image", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                $retVal[] = $entry;
            }
        }
        return $retVal;
    }

    public function get($id) {
        $retVal = null;
        $select = MapperUtil::getDbTable_Gallery()->select()
                ->setIntegrityCheck(false)
                ->from(array("s" => "sa_gallery"));
        $select->where(MapperUtil::getDbTable_Gallery()->getAdapter()->quoteInto("s.id = ?", $id));
        $row = MapperUtil::getDbTable_Gallery()->fetchRow($select);
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Gallery", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function getImage($id) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Image()->fetchRow(MapperUtil::getDbTable_Image()->getAdapter()->quoteInto("id = ?", $id));
        if ($row != null) {
            $retVal = MapperUtil::toObject("SA_Entity_Image", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        }
        return $retVal;
    }

    public function update(SA_Entity_Gallery $gallery) {
        $data = MapperUtil::mapObject($gallery, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        unset($data['images']);
        //update
        MapperUtil::getDbTable_Gallery()->update($data, MapperUtil::getDbTable_Gallery()->getAdapter()->quoteInto("id = ?", $gallery->getId()));
    }

    public function updateImage(SA_Entity_Image $image) {
        $data = MapperUtil::mapObject($image, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
        unset($data["id"]);
        //update
        MapperUtil::getDbTable_Image()->update($data, MapperUtil::getDbTable_Image()->getAdapter()->quoteInto("id = ?", $image->getId()));
    }

    public function createImages($imgs) {
        foreach ($imgs as $img) {
            $this->createImage($img);
        }
    }

}
