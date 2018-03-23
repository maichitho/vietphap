<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DateTimeUtil
 *
 * @author THOQ-LUONG
 * May 8, 2014 2:38:33 PM
 */
class DateTimeUtil {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    public static function toSqlString( DateTime $dateTime ) {
        $retVal = null;
        if ( $dateTime != null ) {
            $retVal = $dateTime->format("Y:m:d H:i:s");
        }
        return $retVal;
    }

    public static function parseSqlString( $datetimeString ) {
        $retVal = null;
        if ( $datetimeString != null ) {
            $retVal = DateTime::createFromFormat("Y-m-d H:i:s", $datetimeString);
        }
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Implement N Override
    //--------------------------------------------------------------------------
    //  Utils
    //--------------------------------------------------------------------------
    //  Inner class
}
