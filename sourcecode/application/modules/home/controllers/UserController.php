<?php

class Home_UserController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->getMenu();
    }

    public function indexAction() {
//        $this->renderScript("supplier/supplier-list.phtml");
    }

    public function loginAction() {
        //check type of user to redirect

        $authenService = Services::createAuthenticationService();
        if (!$authenService->isAuthenticated() && $this->getRequest()->isPost()) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $authenService->login($username, $password, SA_AuthenticationMapper::USER_TYPE_CUSTOMER);

            if ($authenService->isAuthenticated()) {
                $this->redirect("/thong-tin-nguoi-dung");
            } else {
                //disable layout
                $this->view->result = false;
                $this->view->username = $username;
                $this->renderScript('customer/customer-login.phtml');
            }
        } else if ($authenService->isAuthenticated()) {
            $this->redirect("/thong-tin-nguoi-dung");
        } else {
            $this->renderScript('customer/customer-login.phtml');
        }
    }

    public function logoutAction() {
        $authenService = Services::createAuthenticationService();
        $authenService->logout();
        $this->_redirect('/dang-nhap');
    }

    private function getMenu() {
        $configService = Services::createConfigurationService();

        //Menu Header
        $filter = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_HEADER,
            "parentId" => 0,
            "status" => SA_Entity_Menu::STATUS_SHOW,
            "order" => array("order_number", "asc"));
        $this->view->menuRootHeader = ControllerUtils::mapMenus($configService->findMenus($filter));

        $filter = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_HEADER,
            "status" => SA_Entity_Menu::STATUS_SHOW,
            "order" => array("order_number", "asc"));
        $this->view->menuHeader = ControllerUtils::mapMenus($configService->findMenus($filter));

        //Menu Footer
        $filter = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_FOOTER,
            "parentId" => 0,
            "status" => SA_Entity_Menu::STATUS_SHOW,
            "order" => array("order_number", "asc"));
        $this->view->menuRootFooter = ControllerUtils::mapMenus($configService->findMenus($filter));

        $filter = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Menu::TYPE_FOOTER,
            "status" => SA_Entity_Menu::STATUS_SHOW,
            "order" => array("order_number", "asc"));
        $this->view->menuFooter = ControllerUtils::mapMenus($configService->findMenus($filter));
    }

    public function resetPasswordAction() {
        if (!$this->_request->isPost()) {
            $this->renderScript('home/reset-password.phtml');
        } else {
            $success = FALSE;
            $email = $this->getRequest()->getParam('email');
            $users = Services::createCustomerService()->find(array('email' => $email));
            if ($users != NULL && count($users) == 1) {
                //TODO
                $now = new DateTime();
                $stringNow = $now->format('Y-m-d H:i:s');
                $keyreset = md5($email . $stringNow);

                $this->sendResetPassword($email, $keyreset, $stringNow);
                try {
                    $users[0]->setResettime($now);
                    $users[0]->setResetkey($keyreset);
                    Services::createCustomerService()->update($users[0]);
                    $success = TRUE;
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
            if ($success) {
                $this->view->success = TRUE;
            } else {
                $this->view->success = FALSE;
            }
            $this->renderScript('home/reset-password.phtml');
        }
    }

    public function newPasswordAction() {
        if (!$this->_request->isPost()) {
            $resetkey = $this->getRequest()->getParam('resetkey');
            $this->view->resetkey = $resetkey;
            $users = Services::createCustomerService()->find(array('resetkey' => $resetkey));
            if ($users != NULL && count($users) == 1) {
                $this->view->success = FALSE;
            } else {
                $this->view->success = TRUE;
            }
            $this->renderScript('home/new-password.phtml');
        } else {
            $resetkey = $this->getRequest()->getParam('resetkey');
            $interval = DateInterval::createfromdatestring('+1 day');
            $resettime = new DateTime();
            $resettime->sub($interval);
            $password = $this->getRequest()->getParam('password');
            $time = $resettime->format('Y-m-d H:i:s');
            $users = Services::createCustomerService()->find(array('resetkey' => $resetkey, 'resettime' => $time));
            if ($users != NULL && count($users) == 1) {
                $users[0]->setPassword(sha1($password));
                $users[0]->setResetkey("");
                $users[0]->setResettime(NULL);
                Services::createCustomerService()->update($users[0]);
                $this->view->success = TRUE;
                $authenService = Services::createAuthenticationService();

                $authenService->login($users[0]->getEmail(), $users[0]->getPassword(), SA_AuthenticationMapper::USER_TYPE_CUSTOMER);
            } else {
                $this->view->success = FALSE;
            }
            $this->renderScript('home/new-password.phtml');
        }
    }

    private function sendResetPassword($email, $keyreset, $timereset) {
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/home/views/scripts/email/');
        $html->assign('email', urldecode($email));
        $html->assign('keyreset', $keyreset);
        $html->assign('timereset', $timereset);

        $bodyText = $html->render('email-reset-password.phtml');
      
        Util::sendMail($email, $email, Util::translate("email.reset.password"), $bodyText);
    }

}
