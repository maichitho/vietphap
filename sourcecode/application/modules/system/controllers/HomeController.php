<?php

class System_HomeController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
//        $this->view->headerTreeMenus = ControllerUtils::mapMenus($this->sortMenus(Services::createConfigurationService()->findMenus(array('type' => SA_Entity_Menu::TYPE_HEADER))));
//        $this->view->footerTreeMenus = ControllerUtils::mapMenus($this->sortMenus(Services::createConfigurationService()->findMenus(array('type' => SA_Entity_Menu::TYPE_FOOTER))));

        $this->renderScript("home/index.phtml");
    }

    public function asyncUpdateReadNotificationAction() {
        $params = $this->getRequest()->getParams();

        if (isset($params['type'])) {
            Services::createNotificationService()->updateAll($params['type']);
        }
        $this->noRender();
        echo json_encode(array("success" => true));
    }

    public function asyncGetNotificationDataAction() {
        $result = Services::createNotificationService()->getNewCountByType();
        $this->noRender();
        echo json_encode(array("items" => $result, "success" => true));
    }

    private function sortMenus($menus) {
        $retval = array();
        foreach ($menus as $parentMenu) {
            /* @var $parentMenu SA_Entity_Menu */
            if ($parentMenu->getParentId() <= 0) {
                $retval[] = $parentMenu;
                foreach ($menus as $childMenu) {
                    if ($childMenu->getParentId() == $parentMenu->getId()) {
                        $retval[] = $childMenu;
                    }
                }
            }
        }
        return $retval;
    }

}
