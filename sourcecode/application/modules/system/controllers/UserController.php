<?php

class System_UserController extends Zend_Controller_Action {

    const SUCCESS = "SUCCESS";
    const ERROR = "ERROR";

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        
    }

    public function listAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $params = $this->getRequest()->getParams();
        if (isset($params["flag"])) {
            $this->view->flag = $params["flag"];
        }

        //$user = Services::createAuthenticationService()->getUser();
        $this->view->user = MapperUtil::mapObject($user);

        $userTypes = array();
        $userTypes[SA_Entity_User::TYPE_ADMINISTRATOR] = "Admin";
        $userTypes[SA_Entity_User::TYPE_MANAGER] = "QL điểm bán";
        $userTypes[SA_Entity_User::TYPE_EDITOR] = "Biên tập";
        $this->view->userTypes = $userTypes;


        $filter = array("pageSize" => 20,
            "pageId" => 0
        );

        $this->view->users = ControllerUtils::mapUsers(Services::createUserService()->find($filter));
        $this->view->type = "list";
        if (isset($params["type"])) {
            $this->view->type = $params["type"];
        }
        if (isset($params["id"])) {
            $this->view->user = MapperUtil::mapObject(Services::createUserService()->get($params["id"]));
        }
        //status
        $statuses = array();
        $statuses[SA_Entity_User::STATUS_ACTIVE] = Util::translate("form.label.status.active");
        $statuses[SA_Entity_User::STATUS_BLOCK] = Util::translate("form.label.status.block");
        $statuses[SA_Entity_User::STATUS_INACTIVE] = Util::translate("form.label.status.inactive");
        $this->view->statuses = $statuses;

        $this->renderScript("user/user-list.phtml");
    }

    public function createAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $user = Services::createAuthenticationService()->getUser();
        $this->view->users = ControllerUtils::mapUsers(Services::createUserService()->find());

        if (!$this->getRequest()->isPost()) {
            $this->view->user = MapperUtil::mapObject($user);
            $userTypes = array();
            $userTypes[SA_Entity_User::TYPE_ADMINISTRATOR] = "Admin";
            $userTypes[SA_Entity_User::TYPE_MANAGER] = "QL điểm bán";
            $userTypes[SA_Entity_User::TYPE_EDITOR] = "Biên tập";
            $this->view->userTypes = $userTypes;
            //status
            $statuses = array();
            $statuses[SA_Entity_User::STATUS_ACTIVE] = Util::translate("form.label.status.active");
            $statuses[SA_Entity_User::STATUS_BLOCK] = Util::translate("form.label.status.block");
            $statuses[SA_Entity_User::STATUS_INACTIVE] = Util::translate("form.label.status.inactive");
            $this->view->statuses = $statuses;

            $this->view->type = "list";
            $this->renderScript("user/user-list.phtml");
            return;
        }

        $this->view->type = "create";
        // create user for inserting to database
        $params = $this->getRequest()->getPost();

        $userEntity = $this->paramToUser($params, new SA_Entity_User());
        $userEntity->setCreateTime(new DateTime());

        if (isset($params["password"])) {
            $userEntity->setPassword(sha1($params["password"]));
        }
        $userId = Services::createUserService()->create($userEntity);

        if (!$userId) {
            $this->view->user = MapperUtil::mapObject($user);
            $this->redirect("/system/user/list?flag=error&type=create");
        } else {
            $userEntity->setId($userId);
            $this->view->user = MapperUtil::mapObject($userEntity);
            $this->redirect("/system/user/list?flag=success&type=create");
        }
    }

    public function updateAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $params = $this->getRequest()->getParams();
        $user = Services::createAuthenticationService()->getUser();
        $this->view->users = ControllerUtils::mapUsers(Services::createUserService()->find());
        if (!$this->getRequest()->isPost()) {
            //status
            $statuses = array();
            $statuses[SA_Entity_User::STATUS_ACTIVE] = Util::translate("form.label.status.active");
            $statuses[SA_Entity_User::STATUS_BLOCK] = Util::translate("form.label.status.block");
            $statuses[SA_Entity_User::STATUS_INACTIVE] = Util::translate("form.label.status.inactive");
            $this->view->statuses = $statuses;

            $userTypes = array();
            $userTypes[SA_Entity_User::TYPE_ADMINISTRATOR] = "Admin";
            $userTypes[SA_Entity_User::TYPE_MANAGER] = "QL điểm bán";
            $userTypes[SA_Entity_User::TYPE_EDITOR] = "Biên tập";
            $this->view->userTypes = $userTypes;

            $updatedUser = Services::createUserService()->get($params["id"]);
            $this->view->user = MapperUtil::mapObject($updatedUser);
            $this->view->type = "update";
            $this->renderScript("user/user-list.phtml");
            return;
        }

        $this->view->type = "update";

        $updatedUser = Services::createUserService()->get($params["id"]);
        $updatedUser = $this->paramToUser($params, $updatedUser);
//        $updatedUser->setType($user->getType());

        try {
            Services::createUserService()->update($updatedUser);
            $this->view->user = MapperUtil::mapObject($updatedUser);

            $this->redirect("/system/user/list?flag=success");
        } catch (Exception $ex) {
            $this->view->user = MapperUtil::mapObject($user);
            $this->redirect("/system/user/list?flag=error&type=update&id=" . $params["id"]);
        }
    }

    public function logoutAction() {
        $authenService = Services::createAuthenticationService();
        $authenService->logout();
        $this->_redirect('/system/user/login');
    }

    public function loginAction() {
        $this->noRender();
        $authenService = Services::createAuthenticationService();

        if ($this->getRequest()->isPost()) {
            $username = $this->getRequest()->getParam('username');
            $password = $this->getRequest()->getParam('password');
            $authenService->login($username, $password, SA_AuthenticationMapper::USER_TYPE_ADMIN);
            if ($authenService->isAuthenticated()) {
                $this->redirect("/system/news/list");
            } else {
                //disable layout
                $this->view->result = false;
                $this->view->username = $username;
                $this->renderScript('user/login.phtml');
            }
        } else if (!$this->getRequest()->isPost() && $authenService->isAuthenticated()) {
            $this->redirect("/system/news/list");
        } else {
            $this->renderScript('user/login.phtml');
        }
    }

    public function deleteAction() {
        $params = $this->getRequest()->getParams();
        Services::createUserService()->delete($params["id"]);
        $this->_redirect("/system/user/list");
    }

    public function asyncDeleteAction() {
        $status = FALSE;
        $formData = $this->getRequest()->getParams();

        try {
            if (key_exists("id", $formData)) {
                Services::createUserService()->delete($formData["id"]);
                $status = TRUE;
            }
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncListAction() {
//        $status = FALSE;
//        $formData = $this->getRequest()->getParams();
//        try {
//            if (key_exists("keyword", $formData) && $formData["isSupplier"] == "") {
////                $users = ControllerUtils::mapUsers(Services::createUserService()->find(array("type" => SA_Entity_User::TYPE_SUPPLIER,
////                                    "keyword" => $formData["keyword"])));
////                $status = TRUE;
//            } else {
//                $users = array();
//                $suppliers = Services::createSupplierService()->find(array("name" => $formData["keyword"]));
//                if ($suppliers != null && count($suppliers) > 0) {
//                    foreach ($suppliers as $supplier) {
//                        $temUsers = Services::createUserService()->find(array("type" => SA_Entity_User::TYPE_SUPPLIER,
//                            "supplierId" => $supplier->getId()));
//                        if ($temUsers != null && count($temUsers)) {
//                            $users = array_merge_recursive($users, $temUsers);
//                        }
//                    }
//                }
//                $users = ControllerUtils::mapUsers($users);
//                $status = TRUE;
//            }
//        } catch (Exception $ex) {
//            $status = FALSE;
//        }
//        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);
//        echo json_encode(array("users" => $users));
    }

    public function changePasswordAction() {
        if (!$this->_request->isPost()) {
            $this->view->user = MapperUtil::mapObject(Services::createAuthenticationService()->getUser());
            $this->renderScript("user/user-change-password.phtml");
            return;
        }

        $result = "error";
        $params = $this->getRequest()->getParams();
        $user = Services::createAuthenticationService()->getUser();
        if ($user->getPassword() != sha1($params["password"])) {
            $this->view->message = Util::translate("message.changepassword.error.current_password");
            $result = "error";
        } else {
            $user->setPassword(sha1($params["newPassword"]));
            $result = "success";
            Services::createUserService()->update($user);
        }

        $this->view->flag = $result;
        $this->renderScript("user/user-change-password.phtml");
    }

    public function isExistingUserAction() {
        $status = FALSE;
        $formData = $this->getRequest()->getParams();

        try {
            if (key_exists("username", $formData)) {
                $filter["username"] = $formData["username"];
                $users = Services::createUserService()->find($filter);
                if (count($users) > 0) {
                    $status = TRUE;
                }
            }
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function isExistingEmailAction() {
        $status = FALSE;
        $formData = $this->getRequest()->getParams();

        try {
            if (key_exists("email", $formData)) {
                $filter["email"] = $formData["email"];
                $users = Services::createUserService()->find($filter);
                if (count($users) > 0) {
                    $status = TRUE;
                }
            }
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncResetPasswordUserAction() {
        $status = FALSE;
        $formData = $this->getRequest()->getParams();

        try {
            if (key_exists("userId", $formData)) {
                $user = Services::createUserService()->get($formData["userId"]);
                if ($user != null) {
                    $newPassWord = Util::randomString(8);
                    $user->setPassword(sha1($newPassWord));
                    Services::createUserService()->update($user);

                    //Send Email to user to give him/her new password
                    $html = new Zend_View();
                    $html->setScriptPath(APPLICATION_PATH . '/modules/system/views/scripts/email/');
                    // assign valeues
                    $html->assign('fullName', urldecode($user->getFullName()));
                    $html->assign('username', $user->getUsername());
                    $html->assign('password', $newPassWord);
                    // render view
                    $bodyText = $html->render('reset-password-notification.phtml');

                    Util::sendMail(urldecode($user->getEmail()), urldecode($user->getFullName()), Util::translate("email.reset_password.subject"), $bodyText);
                    $status = TRUE;
                }
            }
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    private function paramToUser($param, $user) {

        if (isset($param["fullName"])) {
            $user->setFullName($param["fullName"]);
        }
        if (isset($param["username"])) {
            $user->setUsername($param["username"]);
        }
        if (isset($param["email"])) {
            $user->setEmail($param["email"]);
        }
        if (isset($param["status"])) {
            $user->setStatus($param["status"]);
        }

        if (isset($param["userType"])) {
            $user->setType($param["userType"]);
        }

        $user->setUpdateTime(new DateTime());
        return $user;
    }

}
