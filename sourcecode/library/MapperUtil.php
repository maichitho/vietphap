<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Sililab
 */
class MapperUtil {

    const PROPERTY_TYPE_CAMEL = "aB";
    const PROPERTY_TYPE_UNDERSCORE = "_";
    const DATE_FORMAT = 'd/m/Y';

    private static $dbTableCategory;
    private static $dbTableLocation;
    private static $dbTableParam;
    private static $dbTableEntry;
    private static $dbTableMenu;
    private static $dbTableUser;
    private static $dbTableTag;
    private static $dbTableNotification;
    private static $dbTableWorkshop;
    private static $dbTableGallery;
    private static $dbTableImage;
    private static $dbTableDrugstore;
    private static $dbTableRelatedEntry;

    public static function getDbTable_Notification() {
        if (MapperUtil::$dbTableNotification == null) {
            MapperUtil::$dbTableNotification = new SA_DbTable_Notification();
        }
        return MapperUtil::$dbTableNotification;
    }

    public static function getDbTable_Workshop() {
        if (MapperUtil::$dbTableWorkshop == null) {
            MapperUtil::$dbTableWorkshop = new SA_DbTable_Workshop();
        }
        return MapperUtil::$dbTableWorkshop;
    }

    public static function getDbTable_Drugstore() {
        if (MapperUtil::$dbTableDrugstore == null) {
            MapperUtil::$dbTableDrugstore = new SA_DbTable_Drugstore();
        }
        return MapperUtil::$dbTableDrugstore;
    }

    public static function getDbTable_Gallery() {
        if (MapperUtil::$dbTableGallery == null) {
            MapperUtil::$dbTableGallery = new SA_DbTable_Gallery();
        }
        return MapperUtil::$dbTableGallery;
    }

    public static function getDbTable_Image() {
        if (MapperUtil::$dbTableImage == null) {
            MapperUtil::$dbTableImage = new SA_DbTable_Image();
        }
        return MapperUtil::$dbTableImage;
    }

    public static function getDbTable_Menu() {
        if (MapperUtil::$dbTableMenu == null) {
            MapperUtil::$dbTableMenu = new SA_DbTable_Menu();
        }
        return MapperUtil::$dbTableMenu;
    }

    public static function getDbTable_Tag() {
        if (MapperUtil::$dbTableTag == null) {
            MapperUtil::$dbTableTag = new SA_DbTable_Tag();
        }
        return MapperUtil::$dbTableTag;
    }

    public static function getDbTable_User() {
        if (MapperUtil::$dbTableUser == null) {
            MapperUtil::$dbTableUser = new SA_DbTable_User();
        }
        return MapperUtil::$dbTableUser;
    }

    public static function getDbTable_Param() {
        if (MapperUtil::$dbTableParam == null) {
            MapperUtil::$dbTableParam = new SA_DbTable_Param();
        }
        return MapperUtil::$dbTableParam;
    }

    public static function getDbTable_Entry() {
        if (MapperUtil::$dbTableEntry == null) {
            MapperUtil::$dbTableEntry = new SA_DbTable_Entry();
        }
        return MapperUtil::$dbTableEntry;
    }

    public static function getDbTable_RelatedEntry() {
        if (MapperUtil::$dbTableRelatedEntry == null) {
            MapperUtil::$dbTableRelatedEntry = new SA_DbTable_RelatedEntry();
        }
        return MapperUtil::$dbTableRelatedEntry;
    }
    public static function getDbTable_Category() {
        if (MapperUtil::$dbTableCategory == null) {
            MapperUtil::$dbTableCategory = new SA_DbTable_Category();
        }
        return MapperUtil::$dbTableCategory;
    }

    public static function getDbTable_Location() {
        if (MapperUtil::$dbTableLocation == null) {
            MapperUtil::$dbTableLocation = new SA_DbTable_Location();
        }
        return MapperUtil::$dbTableLocation;
    }

    public static function mapObjects($objectList, $propertypeType = MapperUtil::PROPERTY_TYPE_CAMEL, $dateTimeFormat = "Y-m-d H:i:s") {
        $retval = array();
        foreach ($objectList as $object) {
            $retval[] = MapperUtil::mapObject($object, $propertypeType, $dateTimeFormat);
        }
        return $retval;
    }

    public static function mapObject($object, $propertypeType = MapperUtil::PROPERTY_TYPE_CAMEL, $dateTimeFormat = "Y-m-d H:i:s") {
        if ($propertypeType == null) {
            $propertypeType = MapperUtil::PROPERTY_TYPE_CAMEL;
        }
        if ($dateTimeFormat == null) {
            $dateTimeFormat = "Y-m-d H:i:s";
        }
        $retval = array();
        try {
            $methods = get_class_methods(get_class($object));
            foreach ($methods as $method) {
                $methodReturnClass = MapperUtil::getMethodReturnType(get_class($object), $method);
                if (strpos($method, "get") === 0) {
                    if ($object == NULL) {
                        $methodValue = "";
                    } else {
                        $methodValue = call_user_func(array($object, $method));
                        // $methodValue = call_user_method($method, $object);
                        if ($methodValue == null) {
                            $methodValue = "";
                        }
                    }

                    $arrayElement;
                    $methodClass = FALSE;
                    if (is_string($methodValue) || is_numeric($methodValue)) {
                        
                    } else {
                        $methodClass = get_class($methodValue);
                    }
                    if ($methodClass !== FALSE) {
                        if ($methodClass == "DateTime") {
                            if ($dateTimeFormat != null) {
                                $arrayElement = ($methodValue->format($dateTimeFormat));
                            } else {
                                $arrayElement = ($methodValue->format("Y-m-d H:i:s"));
                            }
                        } else {
                            $arrayElement = MapperUtil::mapObject($methodValue, $propertypeType, $dateTimeFormat);
                        }
                    } else {
                        $arrayElement = $methodValue;
                    }
                    $retval[MapperUtil::getPropertyFromGetterSetter($method, FALSE, $propertypeType)] = $arrayElement;
                }
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $retval;
    }

    public static function toObject($className, $objectMap, $propertypeType = MapperUtil::PROPERTY_TYPE_CAMEL) {
        $retVal = new $className();
        $methods = get_class_methods($className);
        foreach ($methods as $method) {
            if (strpos($method, "get") === 0) {
                $attr = MapperUtil::getPropertyFromGetterSetter($method, FALSE, $propertypeType);
                if (isset($objectMap[$attr])) {
                    $attrValue = $objectMap[$attr];
                    $elementValue;
                    $methodReturnClass = MapperUtil::getMethodReturnType($className, $method);
//                    if ( $methodReturnClass == "Ref" && $propertypeType == MapperUtil::PROPERTY_TYPE_UNDERSCORE ) {
//                        continue;
//                    } else 
                    if ($methodReturnClass == "DateTime") {
                        if ($attrValue != "0000-00-00 00:00:00") {
                            $elementValue = DateTime::createFromFormat("Y-m-d H:i:s", $attrValue);
                        } else {
                            $elementValue = "";
                            //$elementValue = $attrValue;
                        }
                    } else {
                        $methodClass = is_array($attrValue);
                        if (is_array($attrValue)) {
                            $elementValue = MapperUtil::toObject($methodReturnClass, $attrValue, $propertypeType);
                        } else {
                            $elementValue = $attrValue;
                        }
                    }
                    $setMethod = MapperUtil::getGetterSetterFromProperty($attr, TRUE, MapperUtil::PROPERTY_TYPE_UNDERSCORE);
                    call_user_func(array($retVal, $setMethod), $elementValue);
//                    call_user_method( $setMethod, $retVal, $elementValue );
                }
            }
        }
        return $retVal;
    }

    public static function getPropertyFromGetterSetter($methodName, $isSetter = TRUE, $propertypeType = MapperUtil::PROPERTY_TYPE_CAMEL) {
        $retval = "";
        if ($isSetter) {
            $retval = lcfirst(str_replace("set", "", $methodName));
        } else {
            $retval = lcfirst(str_replace("get", "", $methodName));
        }
        if ($propertypeType == MapperUtil::PROPERTY_TYPE_CAMEL) {
//            $retval = lcfirst( $retval );
        } else if ($propertypeType == MapperUtil::PROPERTY_TYPE_UNDERSCORE) {
            $retval = strtolower(preg_replace('/([A-Z])/', '_$1', $retval));
        }
        return $retval;
    }

    private static function getGetterSetterFromProperty($propertyName, $isSetter = TRUE, $propertypeType = MapperUtil::PROPERTY_TYPE_CAMEL) {
        $retval = "";
        if ($propertypeType == MapperUtil::PROPERTY_TYPE_CAMEL) {
            
        } else if ($propertypeType == MapperUtil::PROPERTY_TYPE_UNDERSCORE) {
            $propertyExplodes = explode("_", $propertyName);
            foreach ($propertyExplodes as $propertyExplode) {
                $retval.=ucfirst($propertyExplode);
            }
        }
        if ($isSetter) {
            $retval = "set" . ucfirst($retval);
        } else {
            $retval = "get" . ucfirst($retval);
        }
        return $retval;
    }

    public static function getMethodReturnType($className, $methodName) {
        $refClass = new ReflectionClass($className);
        $docComment = $refClass->getMethod($methodName)->getDocComment();
        preg_match('/@return\s+([^\s]+)/', $docComment, $matches);
        if ($matches != NULL && count($matches) > 0) {
            list(, $type) = $matches;
        } else {
            $type = NULL;
        }
        return $type;
    }

}

?>
