<?php

/**
 * Description of ControllerUtils
 *
 * @author Sililab
 */
class ControllerUtils {

    const FIX_LINK_MOM_CHILD = "/me-be";
    const FIX_LINK_DISTRIBUTION = "/diem-ban";

    public function ControllerUtils() {
        
    }

    public function getMenu() {
        
    }

    public static function getParamByKey($paramarr, $key) {
        foreach ($paramarr as $param) {
            if ($param->getKey2() === $key)
                return $param;
        }
        return null;
    }

    public static function validatePermision($view) {
        $user = Services::createAuthenticationService()->getUser();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        if ($user->getType() === SA_Entity_User::TYPE_EDITOR) {
            if ($controllerName == "user" || $controllerName == "setting" || $controllerName == "category" || $controllerName == "drugstore") {
                $msg = "Bạn không có quyền thực hiện chức năng này";
                $view->rightMsg = $msg;
                return msg;
            }
        }
        if ($user->getType() === SA_Entity_User::TYPE_MANAGER) {
            if ($controllerName == "user" || $controllerName == "setting" || $controllerName == "category" || $controllerName == "news") {
                $msg = "Bạn không có quyền thực hiện chức năng này";
                $view->rightMsg = $msg;
                return msg;
            }
        }
        return "";
    }

    public static function prepareMenuData($view) {

        $configService = Services::createConfigurationService();

        $view->hotline = $configService->get('company.hotline')->getValue();
        $view->email = $configService->get('home.company.email')->getValue();
        $view->companyLogo = $configService->get('company.logo')->getValue();
        $view->companyLogoMobile = $configService->get('company.logo.mobile')->getValue();
        $view->companySlogan = $configService->get('company.banner.slogan')->getValue();
        $view->companySloganMobile = $configService->get('company.banner.slogan.mobile')->getValue();
        $view->bannerImage = $configService->get('company.banner.image')->getValue();

        $view->linkFacebook = $configService->get('facebook.fanpage.link')->getValue();
        $view->boxFacebook = $configService->get('facebook.fanpage.box')->getValue();
        $view->linkGoogle = $configService->get('google.plus.link')->getValue();
        $view->linkTwitter = $configService->get('twitter.link')->getValue();

//        $view->fSlogan = $configService->get('footer.slogan')->getValue();
//        $view->footerAvatar = $configService->get('footer.top.image')->getValue();
//        $view->footerAvatarText = $configService->get('footer.top.text')->getValue();
//        $view->footerAvatarLink = $configService->get('footer.top.link')->getValue();
        //Filter for Menus
        $filterRootHeader = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_HEADER,
            "parentId" => 0,
            "status" => '1',
            "order" => array("order_number", "asc"));

        $filterheader = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_HEADER,
            "status" => '1',
            "order" => array("order_number", "asc"));

        $filterRootFooter = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_FOOTER,
            "parentId" => 0,
            "status" => '1',
            "order" => array("order_number", "asc"));

        $filterfooter = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_HEADER,
            "status" => '1',
            "order" => array("order_number", "asc"));

        $filterleft = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_LEFT,
            "status" => '1',
            "order" => array("order_number", "asc"));

        $filterright = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_RIGHT,
            "status" => '1',
            "order" => array("order_number", "asc"));

//
//        //Gallery
//        $galleryService = Services::createGalleryService();
//        $topgallery = $galleryService->find(array("pageSize" => 1, "pageId" => 0, "top" => TRUE));
//
//        if ($topgallery != null && is_numeric($topgallery[0]->getId())) {
//            $images = $galleryService->findImage(array("pageSize" => 10, "pageId" => 0, "galleryId" => $topgallery[0]->getId()));
//            $imageMap = MapperUtil::mapObjects($images, null, Util::$DATE_FORMAT);
//        }
//        $view->catalogImages = $imageMap;
//        $view->qaEntries = $qaEntriesMap;
//        $view->workshopMap = $workshopMap;
//        $view->supportsMap = $supportsMap;
//
        $view->menuRootHeader = ControllerUtils::mapMenus($configService->findMenus($filterRootHeader));
        $view->menuHeader = ControllerUtils::mapMenus($configService->findMenus($filterheader));
        $view->menuRootFooter = ControllerUtils::mapMenus($configService->findMenus($filterRootFooter));
        $view->menuFooter = ControllerUtils::mapMenus($configService->findMenus($filterfooter));
        $view->menuLeft = ControllerUtils::mapMenus($configService->findMenus($filterleft));
        $view->menuRight = ControllerUtils::mapMenus($configService->findMenus($filterright));
    }

    public static function mapItems($items) {
        $retval = array();
        foreach ($items as $item) {
            $retval[] = MapperUtil::mapObject($item);
        }
        return $retval;
    }

    public static function mapUsers($users) {
        $retval = array();
        foreach ($users as $user) {
            $retval[] = MapperUtil::mapObject($user);
        }
        return $retval;
    }

    public static function mapNotifications($notifications) {
        $retval = array();
        foreach ($notifications as $notification) {
            $retval[] = array('id' => $notification->getId(),
                'type' => $notification->getType(),
                'content' => $notification->getContent(),
                'time' => $notification->getTime(),
                'new' => $notification->getNew(),
                'displayTime' => Util::timeElapsedString($notification->getTime()->getTimestamp()));
        }
        return $retval;
    }

    public static function mapTestimonials($testimonials) {
        $retval = array();

        foreach ($testimonials as $notification) {
            $retval[] = array('author' => $notification->getName(),
                'company' => "",
                'des' => $notification->getDescription(),
                'src' => $notification->getImagePath()
            );
        }
        return $retval;
    }

    public static function mapSuppliers($suppliers) {
        $retval = array();
        foreach ($suppliers as $supplier) {
            $retval[] = MapperUtil::mapObject($supplier, MapperUtil::PROPERTY_TYPE_CAMEL, MapperUtil::DATE_FORMAT);
        }
        return $retval;
    }

    public static function mapDonateItems($items) {
        $retval = array();
        foreach ($items as $item) {
            $retval[] = MapperUtil::mapObject($item, NULL, Util::$DATE_FORMAT);
        }
        return $retval;
    }

    public static function mapCustomerEvaluations($evaluations) {
        $retval = array();
        foreach ($evaluations as $evaluation) {
            $retval[] = MapperUtil::mapObject($evaluation);
        }
        return $retval;
    }

    public static function mapCustomerEvaluation($evaluation) {
        $retval = array(
            'id' => $evaluation->getId(),
            'creatorName' => $evaluation->getCreatorName(),
            'avatarUrl' => $evaluation->getAvatarUrl(),
            'content' => $evaluation->getContent(),
            'creatorCompany' => $evaluation->getCreatorCompany(),
            'creatorEmail' => $evaluation->getCreatorEmail(),
            'isShow' => $evaluation->getIsShow(),
            'createTime' => ($evaluation->getCreateTime()->format(Util::$DATE_FORMAT)),
            'updateTime' => ($evaluation->getUpdateTime()->format(Util::$DATE_FORMAT))
        );
        return $retval;
    }

    public static function mapOrders($orders) {
        $retval = array();
        foreach ($orders as $order) {
            $retval[] = MapperUtil::mapObject($order, NULL, Util::$DATE_FORMAT);
        }
        return $retval;
    }

    public static function mapProductCategories($categories) {
        $retval = array();
        foreach ($categories as $category) {
            $retval[] = MapperUtil::mapObject($category);
        }
        return $retval;
    }

    public static function mapProductCategory($category) {
        $retval = array(
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'orderNumber' => $category->getOrderNumber(),
            'languageCode' => $category->getLanguageCode(),
            'parentId' => $category->getParentId(),
            'thumbnailUrl' => $category->getThumbnailUrl(),
            'homeUrl' => $category->getHomeUrl(),
            'type' => $category->getType(),
            'createTime' => ($category->getCreateTime()->format(Util::$DATE_FORMAT)),
            'updateTime' => ($category->getUpdateTime()->format(Util::$DATE_FORMAT))
        );
        return $retval;
    }

    public static function mapEntrysHaveCategory($entrys, $categorys) {
        $retval = array();
        foreach ($entrys as $entry) {
            $tmp = MapperUtil::mapObject($entry, MapperUtil::PROPERTY_TYPE_CAMEL, MapperUtil::DATE_FORMAT);
            $tmp['categoryName'] = ControllerUtils::findCategoryById($entry, $categorys);
            $retval[] = $tmp;
        }
        return $retval;
    }

    public static function mapEntryHaveCategory($entry, $categorys) {

        $tmp = MapperUtil::mapObject($entry);
        $tmp['categoryName'] = ControllerUtils::findCategoryById($entry, $categorys);

        return $tmp;
    }

    public static function mapEntrys_FrontEnd($entrys, $mainCategoryId = '') {
        $retval = array();
        foreach ($entrys as $entry) {
            $retval[] = array(
                'id' => $entry->getId(),
                'title' => $entry->getTitle(),
                'description' => $entry->getDescription(),
                'content' => $entry->getContent(),
                'imagePath' => $entry->getImagePath(),
                'createTime' => $entry->getCreateTime(),
                'facebookUrl' => $entry->getFacebookUrl(),
                'googleUrl' => $entry->getGoogleUrl(),
                'tags' => $entry->getTags(),
                'isTop' => $entry->getIsTop(),
                'categoryId' => $entry->getCategoryId(),
                'creatorId' => $entry->getCreatorId(),
                'mainCategoryId' => $mainCategoryId
            );
        }
        return $retval;
    }

    public static function mapSupports($supports) {
        $retval = array();
        foreach ($supports as $support) {
            $tmp = MapperUtil::mapObject($support);
            $info = json_decode($support->getDescription(), true);
            $tmp["mobile"] = $info["MOBILE"];
            $tmp["skype_username"] = $info["SKYPE_USERNAME"];
            $tmp["yahoo_username"] = $info["YAHOO_USERNAME"];
            $retval[] = $tmp;
        }
        return $retval;
    }

    private static function findCategoryById($entry, $categorys) {
        $retval = '';
        if ($entry == null)
            return '';
        foreach ($categorys as $cate) {
            if (intval($cate->getId()) == intval($entry->getCategoryId())) {
                $retval = $cate->getName();
                break;
            }
        }
        return $retval;
    }

    public static function mapCategories($categories) {
        $retval = array();
        foreach ($categories as $category) {
            $retval[] = MapperUtil::mapObject($category);
        }
        return $retval;
    }

    public static function mapCategoriesInSystem($categories, $type) {
        $retval = array();
        foreach ($categories as $category) {
            $tmp = array(
                'id' => $category->getId(),
                'name' => $category->getName(),
                'type' => $type,
                'description' => $category->getDescription(),
                'code' => $category->getCode(),
                'parentId' => $category->getParentId(),
                'imagePath' => $category->getImagePath(),
                'iconPath' => $category->getIconPath(),
                'headerMenu' => $category->getHeaderMenu(),
                 'seoKeyword' => $category->getSeoKeyword(),
                 'seoDescription' => $category->getSeoDescription(),
                'footerMenu' => $category->getFooterMenu(),
            );
            if ($type == SA_Entity_Category::TYPE_SUPPORT) {
                $items = json_decode($category->getDescription(), true);
                $tmp["supportType"] = $items["type"];
                $tmp["username"] = $items["username"];
                $tmp["mobile"] = $items["mobile"];
            }
            $retval[] = $tmp;
        }
        return $retval;
    }

    public static function mapGeneralCategory($category) {
        return MapperUtil::mapObject($category);
    }

    public static function mapComments($comments) {
        $retval = array();
        foreach ($comments as $comment) {
            $retval[] = ControllerUtils::mapComment($comment);
        }
        return $retval;
    }

    public static function mapComment($comment) {
        if ($comment->getCreatorType() == SA_AuthenticationMapper::USER_TYPE_CUSTOMER)
            $comment->setUiCreatorName(Services::createCustomerService()->get($comment->getCreatorId())->getFullName());
        else
            $comment->setUiCreatorName(Services::createUserService()->get($comment->getCreatorId())->getFullName());
        return MapperUtil::mapObject($comment, Null, Util::$DATE_TIMF_FORMAT);
    }

    public static function mapCommentAdmins($comments) {
        $retval = array();
        foreach ($comments as $comment) {
            $retval[] = ControllerUtils::mapCommentAdmin($comment);
        }
        return $retval;
    }

    public static function mapCommentAdmin($comment) {
        $retval = array(
            'id' => $comment->getId(),
            'uiCreatorName' => $comment->getUiCreatorName(),
            'creatorType' => $comment->getCreatorType(),
            'creatorId' => $comment->getCreatorId(),
            'content' => $comment->getContent(),
            'parentId' => $comment->getParentId(),
            'amountChildNode' => ($comment->getAmountChildNode() == null) ? 0 : $comment->getAmountChildNode(),
            'isRead' => ($comment->getIsReadAdmin() == null) ? 0 : $comment->getIsReadAdmin(),
            'isReadChild' => ($comment->getIsReadAdminChild() == null) ? 0 : $comment->getIsReadAdminChild(),
            'createTime' => Util::timeElapsedString($comment->getCreateTime()->getTimestamp(), $comment->getCreateTime())
        );
        return $retval;
    }

    public static function mapCommentCreators($comments) {
        $retval = array();
        foreach ($comments as $comment) {
            $retval[] = ControllerUtils::mapCommentCreator($comment);
        }
        return $retval;
    }

    public static function mapCommentCreator($comment) {
        $retval = array(
            'id' => $comment->getId(),
            'uiCreatorName' => $comment->getUiCreatorName(),
            'creatorType' => $comment->getCreatorType(),
            'creatorId' => $comment->getCreatorId(),
            'content' => $comment->getContent(),
            'parentId' => $comment->getParentId(),
            'amountChildNode' => ($comment->getAmountChildNode() == null) ? 0 : $comment->getAmountChildNode(),
            'isRead' => ($comment->getIsReadCreator() == null) ? 0 : $comment->getIsReadCreator(),
            'isReadChild' => ($comment->getIsReadCreatorChild() == null) ? 0 : $comment->getIsReadCreatorChild(),
            'createTime' => Util::timeElapsedString($comment->getCreateTime()->getTimestamp(), $comment->getCreateTime())
        );
        return $retval;
    }

    public static function mapCustomer($customer) {
        return MapperUtil::mapObject($customer, NULL, Util::$DATE_FORMAT);
    }

    public static function mapSuggestions($suggestions) {
        $retval = array();
        foreach ($suggestions as $s) {
            $retval[] = MapperUtil::mapObject($s);
        }
        return $retval;
    }

    //    Utils
    public static function getMenuLink($menu, $isFrontEnd = 0) {
        $link = "";
        if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_ENTRY) {
            $entry = Services::createEntryService()->get($menu->getLinkId());
            try {
                $link = '/' . $entry->getRewriteUrl();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_CATEGORIES) {
            $link = ($menu->getLinkId() > 0) ? "/" . $menu->getRewriteUrl() : "/";
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_NEWS) {
            $link = ($menu->getLinkId() > 0) ? "/" . $menu->getRewriteUrl() : "/";
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_ONE_CATEGORY) {
            $link = ($menu->getLinkId() > 0) ? "/" . $menu->getRewriteUrl() : "/";
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_IMAGE) {
            $link = $menu->getLinkUrl();
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_SERVICE) {
            $entry = Services::createEntryService()->get($menu->getLinkId());
            $link = '/' . Util::toFriendlyString($entry->getRewriteUrl());
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_SERVICE_CATEGORY) {
            $link = ($menu->getLinkId() > 0) ? "/" . Util::toFriendlyString($menu->getRewriteUrl()) : "/";
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_QA) {
            $link = "/hoi-dap/" . $menu->getLinkId();
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_QA_CATEGORY) {
            $link = ($menu->getLinkId() > 0) ? "/hoi-dap/" . $menu->getLinkId() : "/hoi-dap";
        } else if ($menu->getLinkType() == SA_Entity_Menu::LINK_TYPE_MANUAL) {
            $link = $menu->getRewriteUrl();
        }
        return ($isFrontEnd == 0) ? "<a target='_blank' href='" . $link . "'>" . $link . "</a>" : $link;
    }

    public static function mapCustomerInformation($obj) {
        return array(
            'fullname' => $obj["fullname"],
            'email' => $obj["email"],
            'phone' => $obj["phone"],
            'address' => $obj["address"],
            'company' => $obj["company"],
            'district' => $obj["district"],
            'province' => $obj["province"],
            'countryCode' => $obj["countryCode"],
            'zipCode' => $obj["zipCode"]
        );
    }

    public static function mapPaymentInformation($obj) {
        return array(
            'paymentMethod' => $obj["paymentMethod"],
            'note' => $obj["note"]
        );
    }

    /**
     * 
     * @param DO_Entity_Param $param
     * @return type
     */
    public static function mapParams($params) {
        $retval = array();
        foreach ($params as $param) {
            $retval[] = MapperUtil::mapObject($param, NULL, Util::$DATE_FORMAT);
        }
        return $retval;
    }

    /**
     * 
     * @param SA_Entity_Menu $menu
     * @return type
     */
    public static function mapMenu($menu) {
        return MapperUtil::mapObject($menu, NULL, Util::$DATE_FORMAT);
    }

    public static function mapMenus($menus) {
        $retval = array();
        foreach ($menus as $menu) {
            $retval[] = array(
                'id' => $menu->getId(),
                'parentId' => $menu->getParentId(),
                'code' => $menu->getCode(),
                'type' => $menu->getType(),
                'name' => $menu->getName(),
                'description' => $menu->getDescription(),
                'linkId' => $menu->getLinkId(),
                'linkType' => $menu->getLinkType(),
                'linkUrl' => ControllerUtils::getMenuLink($menu, 1),
                'status' => $menu->getStatus(),
                'orderNumber' => $menu->getOrderNumber(),
                'logoPath' => $menu->getLogoPath(),
                'htmlCode' => $menu->getHtmlCode(),
                'imagePath' => $menu->getImagePath()
            );
        }
        return $retval;
    }

    /**
     * 
     * @param SA_Entity_Menu $entry
     * @return type
     */
    public static function mapEntries($entries) {
        $user = Services::createAuthenticationService()->getUser();
        $retval = array();
        foreach ($entries as $entry) {
            $category = Services::createCategoryService()->get($entry->getCategoryId());
            $creator = Services::createUserService()->get($entry->getCreatorId());
            $right = "true";
//            if (($user->getType() == SA_Entity_User::TYPE_EDITOR && $user->getId() != $entry->getCreatorId()) || ($user->getType() == SA_Entity_User::TYPE_MANAGER && $creator->getType() == SA_Entity_User::TYPE_ADMINISTRATOR)) {
//                $right = "false";
//            }

            $retval[] = array(
                "id" => $entry->getId(),
                "title" => $entry->getTitle(),
                "description" => $entry->getDescription(),
                "right" => $right,
                "categoryName" => isset($category) ? $category->getName() : "-",
                "creatorName" => (isset($creator) ? $creator->getFullName() : ''),
                "createTime" => $entry->getCreateTime()->format(Util::$DATE_FORMAT),
                "imagePath" => $entry->getImagePath(),
                "display" => $entry->getDisplay(),
                "asker" => $entry->getAsker(),
                "askerEmail" => $entry->getAskerEmail(),
                "rewriteUrl" => $entry->getRewriteUrl(),
            );
        }
        return $retval;
    }

    public static function mapEntry($entry) {
        return MapperUtil::mapObject($entry);
    }

    public static function mapWorkshops($workshops) {
        $retval = array();
        foreach ($workshops as $workshop) {
            $retval[] = ControllerUtils::mapWorkshop($workshop);
        }
        return $retval;
    }

    public static function mapWorkshop($workshop) {
        $retval = MapperUtil::mapObject($workshop, null, Util::$DATE_FORMAT);
        $retval["startYear"] = $retval["startTime"] = ($workshop->getStartDate() == null) ? "" : $workshop->getStartDate()->format('Y');
        $retval["startMonth"] = $retval["startTime"] = ($workshop->getStartDate() == null) ? "" : $workshop->getStartDate()->format('m');
        $retval["startDay"] = $retval["startTime"] = ($workshop->getStartDate() == null) ? "" : $workshop->getStartDate()->format('d');
        $retval["startTime"] = ($workshop->getStartDate() == null) ? "" : $workshop->getStartDate()->format('H:i');
        return $retval;
    }

    /**
     * Get all user of system
     * 
     * @return array
     */
    public static function getUserList() {
        $usersArray = array();
        $userService = Services::createUserService();
        $users = $userService->find(array());
        if ($users) {
            for ($i = 0; $i < count($users); $i++) {
                $user = $users[$i];
                $usersArray[$i] = ControllerUtils::mapUser($user);
            }
        }
        //=== test
        $userTest = array(
            "fullName" => "�?ỗ bích Ng�?c",
            "username" => "ngocdb",
            "role" => "admin"
        );
        $usersArray[0] = $userTest;

        return $usersArray;
    }

    public static function getOperatorsInOrganization($organizationId, $fieldList = array(), $pageId = 0, $pageSize = 0) {
        $users = Services::createUserService()->find(array(
            "type" => "o",
            "pageId" => $pageId,
            "pageSize" => $pageSize,
            "organizationId" => $organizationId
        ));

        $callback = function($element) use($fieldList) {
                    return ControllerUtils::mapUser($element, $fieldList);
                };
        return array_map($callback, $users);
    }

    public static function getDepartmentsInOrganization($organizationId, $fieldList = array(), $pageId = 0, $pageSize = 0) {
        $users = Services::createOrganizationService()->findDepartments(array(
            "pageId" => $pageId,
            "pageSize" => $pageSize,
            "organizationId" => $organizationId
        ));

        $callback = function($element) use($fieldList) {
                    return ControllerUtils::mapDepartment($element, $fieldList);
                };
        return array_map($callback, $users);
    }

    public static function getDepartmentsForUser($userId, $fieldList = array(), $pageId = 0, $pageSize = 0) {
        $departmentOfUser = Services::createOrganizationService()->findDepartments(array(
            "userId" => $userId,
            "pageId" => $pageId,
            "pageSize" => $pageSize
        ));

        $callback = function($element) use($fieldList) {
                    return ControllerUtils::mapDepartment($element, $fieldList);
                };
        return array_map($callback, $departmentOfUser);
    }

    public static function getUsingLicense($organizationId, $fieldList = array()) {
        $organization = Services::createOrganizationService()->get(
                $organizationId
        );
        $filter = array(
            "licenseCode" => $organization->getLicense(),
            "pageId" => 0,
            "pageSize" => 1,
            "status" => "active"
        );
        $licenses = Services::createProductService()->findLicenses($filter);
        if (!$licenses || count($licenses) < 1) {
            throw new Exception("Not found license");
        }
        return ControllerUtils::mapLicense($licenses[0], $fieldList);
    }

    /**
     * 
     * @param int $departmentId
     * @param array $newUserIds
     * @param array $oldUserIds
     * @return number of changed element
     */
    public static function updateUserInDepartment($departmentId, $newUserIds, $oldUserIds) {

        $changeCount = 0;
        $willBeDeleted = array_unique(array_diff($oldUserIds, $newUserIds));
        $willBeInserted = array_unique(array_diff($newUserIds, $oldUserIds));

        foreach ($willBeDeleted as $id) {
            Services::createOrganizationService()->leaveUserFromDepartment($departmentId, $id);
            $changeCount--;
        }
        foreach ($willBeInserted as $id) {
            Services::createOrganizationService()->joinUserToDepartment($departmentId, $id);
            $changeCount++;
        }

        return count($oldUserIds) + $changeCount;
    }

    /**
     * 
     * @param int $userId
     * @param array $newDepIds
     * @param array $oldDepIds
     * @return number of changed element
     */
    public static function updateDepartmentOfUser($userId, $newDepIds, $oldDepIds) {
        $changeCount = 0;
        $willBeDeleted = array_unique(array_diff($oldDepIds, $newDepIds));
        $willBeInserted = array_unique(array_diff($newDepIds, $oldDepIds));


        var_dump($oldDepIds, $newDepIds);

        foreach ($willBeDeleted as $id) {
            Services::createOrganizationService()->leaveUserFromDepartment($id, $userId);
            $changeCount--;
        }
        foreach ($willBeInserted as $id) {
            Services::createOrganizationService()->joinUserToDepartment($id, $userId);
            $changeCount++;
        }

        return count($oldDepIds) + $changeCount;
    }

    public static function updateExtraFeaturesOfLicense($licenseId, $newFeatureIds, $oldFeatureIds) {
        $changeCount = 0;
        $willBeDeleted = array_unique(array_diff($oldFeatureIds, $newFeatureIds));
        $willBeInserted = array_unique(array_diff($newFeatureIds, $oldFeatureIds));

        var_dump($licenseId, $newFeatureIds, $oldFeatureIds);
        foreach ($willBeDeleted as $id) {
            Services::createProductService()->removeFeature($licenseId, $id);
            $changeCount--;
        }
        foreach ($willBeInserted as $id) {
            Services::createProductService()->addFeature($licenseId, $id);
            $changeCount++;
        }
    }

    public static function initMultiLanguage() {
//        /Detect if the user has chosen a different language
        $localeCookie = (isset($_COOKIE['langTimweb'])) ? $_COOKIE['langTimweb'] : null;

        if (!empty($localeCookie)) {
            // create Zend_Locale object
            $locale = new Zend_Locale($localeCookie);
//            session_start();
//            $_SESSION["langTim"] = $localeCookie;
        } else {
            // Guesses visitor's locale code base on browser
//            session_start();
//            if (isset($_SESSION["langTim"])) {
//                $localeCookie = $_SESSION["langTim"];
//                setcookie("langTim", $localeCookie, time() + (86400 * 30), "/");
//            }
            $locale = new Zend_Locale('vi-VN');
        }
        Zend_Registry::set('Zend_Locale', $locale);

//        $cache = Zend_Cache::factory('Core', 'File', null, null);
//        Zend_Translate::setCache($cache);
        $translate = new Zend_Translate(array(
            'adapter' => 'ini',
            'content' => APPLICATION_PATH . '/../language/vi_VN.ini',
            'locale' => 'vi-VN'
        ));

        $translate->addTranslation(array(
            'adapter' => 'ini',
            'content' => APPLICATION_PATH . '/../language/en_US.ini',
            'locale' => 'en-US'
        ));

        if ($translate->isAvailable($locale)) {
            $translate->setLocale($locale);
        }

        Zend_Registry::set('Zend_Translate', $translate);
    }

}
