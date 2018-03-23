<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthenticationService
 *
 * @author Sililab
 * @created 2013-12-29 16:49:30
 */
interface SA_AuthenticationService {

    public function getSession();

    /**
     *      
     * @return Boolean
     */
    public function isAuthenticated();

    /**
     *      
     * @return usesrType
     */
    public function getUserType();

    /**
     * 
     * @param type $identity
     * @param type $password
     * @return boolean
     */
    public function login( $userName, $password, $type );

    public function logout();

    /**
     *      
     * @return SA_Entity_User/SA_Entity_Supplier/SA_Entity_Customer
     */
    public function getUser();
}

?>
