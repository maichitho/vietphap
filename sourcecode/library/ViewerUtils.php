<?php

/**
 * Description of ViewerUtils
 *
 * @author Sililab
 */
class ViewerUtils {

    public function ViewerUtils() {
        
    }

    public function getMenuInformation() {
        $configService = Services::createConfigurationService();
        // Header Menu
        $this->view->hotline = $configService->get('company.hotline')->getValue();
//        $this->view->companyLogo = $configService->get('company.logo')->getValue();
//        $this->view->companyLogoMobile = $configService->get('company.logo.mobile')->getValue();
    }

}
