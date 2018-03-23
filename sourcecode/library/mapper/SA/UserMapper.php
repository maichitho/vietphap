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
class SA_UserMapper implements SA_UserService {

    public function create( SA_Entity_User $user ) {
        $data = MapperUtil::mapObject( $user, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
        unset( $data['id'] );
        MapperUtil::getDbTable_User()->insert( $data );
        $retVal = MapperUtil::getDbTable_User()->getAdapter()->lastInsertId();
        return $retVal;
    }

    public function delete( $id ) {
        MapperUtil::getDbTable_User()
                ->delete( MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "id = ?", $id ) );
    }

    public function find( $filter ) {
        $retVal = array();
        if ( key_exists( "metric", $filter ) ) {
            $select = MapperUtil::getDbTable_User()->select()
                    ->from( array( "p" => "sa_user" ), array( "COUNT" => "COUNT(*)" ) );
        } else {
            $select = MapperUtil::getDbTable_User()->select()
                    ->setIntegrityCheck( false )
                    ->from( array( "p" => "sa_user" ) );
        }
        $condition = "1=1 ";
        if ( key_exists( "username", $filter ) ) {
            $condition .= " AND " . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "p.username = ?", $filter["username"] );
        }
        if ( key_exists( "keyword", $filter ) ) {
            $condition .= " AND ( " . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "p.username LIKE ?", "%" . $filter["keyword"] . "%" )
                    . " OR " . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "p.full_name LIKE ?", "%" . $filter["keyword"] . "%" )
                    . " OR " . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "p.email LIKE ?", "%" . $filter["keyword"] . "%" ) . " )";
        }
        if ( key_exists( "email", $filter ) ) {
            $condition .= " AND " . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "p.email = ?", $filter["email"] );
        }
       
        if ( key_exists( "type", $filter ) ) {
            if ( $filter["type"] == SA_Entity_User::TYPE_SUPPLIER ) {
                $select->join( array( 's' => 'sa_supplier' ), 'p.supplier_id = s.id', array( 'supplier_name' => 'name' ) );
            }
            $condition .= " AND " . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "p.type = ?", $filter["type"] );
        }
        if ( key_exists( "createTimeFrom", $filter ) ) {
            $condition .= ' AND ' . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( 'p.create_time >= ?', $filter['createTimeFrom'] );
        }
        if ( key_exists( "createTimeTo", $filter ) ) {
            $condition .= ' AND ' . MapperUtil::getDbTable_User()->getAdapter()->quoteInto( 'p.create_time <= ?', $filter['createTimeTo'] );
        }
        $select->where( $condition )
                ->order( array( 'p.create_time DESC' ) );
        if ( key_exists( "metric", $filter ) ) {
            $row = MapperUtil::getDbTable_User()->fetchRow( $select );
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
            $resultSet = MapperUtil::getDbTable_User()->fetchAll( $select );
            foreach ( $resultSet as $row ) {
                $entry = MapperUtil::toObject( "SA_Entity_User", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
                $retVal[] = $entry;
            }
        }
        return $retVal;
    }

    public function get( $id ) {
        $retVal = null;
        $row = MapperUtil::getDbTable_User()->fetchRow( MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "id = ?", $id ) );
        if ( $row != null ) {
            $retVal = MapperUtil::toObject( "SA_Entity_User", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
        }
        return $retVal;
    }

    public function getByUsername( $username ) {
        $retVal = null;
        $row = MapperUtil::getDbTable_User()->fetchRow( MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "username = ?", $username ) );
        if ( $row != null ) {
            $retVal = MapperUtil::toObject( "SA_Entity_User", $row, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
        }
        return $retVal;
    }

    public function update( SA_Entity_User $user ) {
        try {
            $data = MapperUtil::mapObject( $user, MapperUtil::PROPERTY_TYPE_UNDERSCORE );
            unset( $data["id"] );
          
            //update
            MapperUtil::getDbTable_User()
                    ->update( $data, MapperUtil::getDbTable_User()->getAdapter()->quoteInto( "id = ?", $user->getId() ) );
        } catch ( Exception $ex ) {
            var_dump( $ex );
            //MapperUtil::getDbTable_User()->getAdapter()->rollBack();
            throw $ex;
        }
    }

}
