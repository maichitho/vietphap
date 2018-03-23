<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plugin_Layout
 *
 * @author Sililab
 */
class Plugin_Layout extends Zend_Controller_Plugin_Abstract {

    //put your code here
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        //TODO verify authentication here ==============
        $authenIsAuthentication = (Services::createAuthenticationService()->isAuthenticated());
        $authenModuleSystem = ($moduleName == 'system');

        if ($authenModuleSystem && !$authenIsAuthentication) {
            $request->setModuleName('system');
            $request->setControllerName('user');
            $request->setActionName('login');
        } else if ($authenModuleSystem) {
            if ($controllerName == null) {
                $request->setControllerName('news');
            } else {
                $request->setControllerName($controllerName);
            }
            if ($actionName == null) {
                $request->setActionName('list');
            } else {
                $request->setActionName($actionName);
            }
        }

        //=================LAYOUT================

        $layout = Zend_Layout::getMvcInstance();
        switch ($moduleName) {
            case 'home': {
                    $layout->setLayout('home/layout');
                    break;
                }
            case 'system': {
                    $layout->setLayout('system/layout');
                    break;
                }

            default : {
                    $layout->disableLayout();
                }
        }
    }

}

?>
