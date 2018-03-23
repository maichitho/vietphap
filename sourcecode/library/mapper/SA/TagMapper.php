<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TagMapper
 *
 * @author ThoMC
 */
class SA_TagMapper implements SA_TagService {

    public function create( SA_Entity_Tag $tag ) {
        $data = MapperUtil::mapObject( $tag, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
        unset( $data['id'] );
        MapperUtil::getDbTable_Tag()->insert( $data );
        $retVal = MapperUtil::getDbTable_Tag()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete( $id ) {
        MapperUtil::getDbTable_Tag()
                ->delete( MapperUtil::getDbTable_Tag()->getAdapter()->quoteInto( "id = ?", $id ) );
    }

    public function find( $filter ) {
        $retVal = array();
        if ( key_exists( "metric", $filter ) ) {
            $select = MapperUtil::getDbTable_Tag()->select()
                    ->from( array( "s" => "sa_tag" ), array( "COUNT" => "COUNT(*)" ) );
        } else {
            $select = MapperUtil::getDbTable_Tag()->select()
                    ->from( array( "s" => "sa_tag" ) );
        }
        $condition = "1=1 ";
        if ( key_exists( "keyword", $filter ) ) {
            $condition .= " AND " . MapperUtil::getDbTable_Tag()->getAdapter()->quoteInto( "s.name LIKE ?", "%" . $filter["keyword"] . "%" );
        }
        $select->where($condition);
        if ( key_exists( "metric", $filter ) ) {
            $row = MapperUtil::getDbTable_Tag()->fetchRow( $select );
            $recordsCount = $row->COUNT;
            if ( $filter["metric"] == "pageCount" ) {
                $retVal = Util::recordsCountToPagesCount( $recordsCount, $filter["pageSize"] );
            } else {
                $retVal = $recordsCount;
            }
        } else {
            if ( isset( $filter["pageId"] ) && isset( $filter["pageSize"] ) && ($filter["pageId"] != 0 || $filter["pageSize"] != 0 ) ) {
                $select->limitPage( $filter["pageId"] + 1, $filter["pageSize"] );
            }
            $resultSet = MapperUtil::getDbTable_Tag()->fetchAll( $select );
            foreach ( $resultSet as $row ) {
                $tag = MapperUtil::toObject( "SA_Entity_Tag", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
                $retVal[] = $tag;
            }
        }
        return $retVal;
    }

    public function get( $id ) {
        $retVal = null;
        $row = MapperUtil::getDbTable_Tag()->fetchRow( MapperUtil::getDbTable_Tag()->getAdapter()->quoteInto( "id = ?", $id ) );
        if ( $row != null ) {
            $retVal = MapperUtil::toObject( "SA_Entity_Tag", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
        }
        return $retVal;
    }

    public function update( SA_Entity_Tag $tag ) {
        $data = MapperUtil::mapObject( $tag, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
        unset( $data["id"] );
        //update
        MapperUtil::getDbTable_Tag()->update( $data, MapperUtil::getDbTable_Tag()->getAdapter()->quoteInto( "id = ?", $tag->getId() ) );
    }

}
