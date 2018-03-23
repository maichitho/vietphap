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
class SA_AuthenticationMapper implements SA_AuthenticationService {

    const USER_TYPE_SUPPLIER = "supplier";
    const USER_TYPE_CUSTOMER = "customer";
    const USER_TYPE_ADMIN = "admin";

    private $sessionNs = NULL;

    public function getSession() {
        if (isset($this->sessionNs) && $this->sessionNs != NULL) {
            return $this->sessionNs;
        } else {
            $this->sessionNs = new Zend_Session_Namespace('Default', false);
        }
        return $this->sessionNs;
    }

    public function getUser() {
        if (isset($this->getSession()->user) && $this->getSession()->user != NULL) {
            return $this->getSession()->user;
        }
        return null;
    }

    public function isAuthenticated() {
        if (isset($this->getSession()->authenticated) && $this->getSession()->authenticated != NULL) {
            return $this->getSession()->authenticated;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param type $identity
     * @param type $password
     * @return boolean
     */
    public function login($identity, $password, $type) {
        $retval = false;
        /* @var $user SA_Entity_Customer | SA_Entity_User */

        $user = null;
        $encryptedPassword = sha1($password);
        switch ($type) {
            case SA_AuthenticationMapper::USER_TYPE_CUSTOMER:
                $userService = Services::createCustomerService();
                $user = $userService->getByIndentity($identity);
                break;
            case SA_AuthenticationMapper::USER_TYPE_SUPPLIER:
            case SA_AuthenticationMapper::USER_TYPE_ADMIN:
                $userService = Services::createUserService();
                $user = $userService->getByUsername($identity);
                break;
            default:
                break;
        }
        if ($user != null && $user->getPassword() == $encryptedPassword &&
                $user->getStatus() == SA_Entity_User::STATUS_ACTIVE) {
            $retval = true;
            $this->getSession()->userType = $type;
            $this->getSession()->authenticated = true;
            $this->getSession()->user = $user;
        }
        //return
        return $retval;
    }

    public function logout() {
        Zend_Session::destroy(true);
    }

    public function getUserType() {
        return $this->getSession()->userType;
    }

//put your code here
}
