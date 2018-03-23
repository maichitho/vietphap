<?php

class System_DrugstoreController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
// action body
    }

    public function listAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }

        $params = $this->getRequest()->getParams();
        $filter = $this->getFilterForListDrugstore($params);

        $drugstores = MapperUtil::mapObjects(Services::createDrugstoreService()->find($filter));
        $filter ['metric'] = "record-count";
        $pageCount = Services::createDrugstoreService()->find($filter);
        $this->view->drugstores = isset($drugstores) ? $drugstores : array();

//paginator        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
        $paginator = Zend_Paginator::factory(range(1, $pageCount));
        $paginator->setCurrentPageNumber($this->getPageNumber());
        $paginator->setItemCountPerPage($filter['pageSize']);
        $this->view->paginator = $paginator;

//Set search conditions
        $this->view->keyword = isset($this->view->keyword) ? $this->view->keyword : '';
        $this->view->address = isset($this->view->address) ? $this->view->address : '';
        $this->view->cityId = isset($this->view->cityId) ? $this->view->cityId : '0';
        $this->view->districtId = isset($this->view->districtId) ? $this->view->districtId : '0';

//get data for selections
        $this->view->cities = ControllerUtils::mapItems(Services::createCategoryService()->findLocation(array("parentId" => 0,
                            "orders" => array(array("column" => "lft", "type" => "asc")))));
        $firstElement = array("id" => "0", "parentId" => 0, "name" => Util::translate("form.select.option.all"));
        array_unshift($this->view->cities, $firstElement);

        if ($this->view->cityId != "0") {
            $this->view->districts = ControllerUtils::mapItems(Services::createCategoryService()->findLocation(array("parentId" => $this->view->cityId,
                                "orders" => array(array("column" => "lft", "type" => "asc")))));
        } else {
            $this->view->districts = array();
        }
        array_unshift($this->view->districts, $firstElement);

        //render        
        $this->renderScript("drugstore/drugstore-list.phtml");
    }

    public function asyncUploadExcelAction() {
        $status = FALSE;
        include 'Classes/PHPExcel.php';
        include 'Classes/PHPExcel/IOFactory.php';
        try {
            $upload_handler = new UploadHandler();
            $status = TRUE;
//            // This is the file path to be uploaded.
            $inputFileName = $upload_handler->filePath;
            // var_dump($inputFileName);
//            $inputFileName = "a.xlsx";
//
            try {
                $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
//
////
                $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
//

                $params = $this->getRequest()->getParams();

                if (isset($params["cityId"])) {
                    $cityId = $params["cityId"];
                }
                if (isset($params["districtId"])) {
                    $districtId = $params["districtId"];
                }

//
//            //Xóa các nhà thuốc ở khu vực này
                if ($params["deleteData"] == "on") {
                    Services::createDrugstoreService()->deleteByLocation($cityId, $districtId);
                }
            } catch (Exception $e) {
                $status = FALSE;
            }
            for ($i = 4; $i <= $arrayCount; $i++) {
                $stt = trim($allDataInSheet[$i]["A"]);
                $tennhathuoc = trim($allDataInSheet[$i]["B"]);
                $diachi = trim($allDataInSheet[$i]["C"]);
                $sdt = trim($allDataInSheet[$i]["D"]);

                $drugstore = new SA_Entity_Drugstore();
                $drugstore->setStatus('1');

                $drugstore->setCityId($cityId);
                $drugstore->setDistrictId($districtId);
                $drugstore->setName($tennhathuoc);
                $drugstore->setAddress($diachi);
                $drugstore->setPhone($sdt);
                $drugstore->setOrderNumber($stt);

                $drugstore->setUpdateTime(new DateTime());

                $drugstore->setCreateTime(new DateTime());
                Services::createDrugstoreService()->create($drugstore);
            }
        } catch (Exception $ex) {
            $status = FALSE;
        }

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //    echo json_encode(array("status" => $status, "fileName", $inputFileName));
    }

    public function createAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();
            //var_dump($this->buildEntry(new SA_Entity_Entry(), $params));
            Services::createDrugstoreService()->create($this->buildDrugstore(new SA_Entity_Drugstore(), $params));
            $this->view->result = true;
            $this->_redirect("/system/drugstore/list");
        }

        $drugstore = new SA_Entity_Drugstore();
        $drugstore->setStatus('1');
        $drugstore->setOrderNumber(intval(Services::createDrugstoreService()->getMaxOrderNumber()) + 1);

        $this->view->drugstore = MapperUtil::mapObject($drugstore);
        $this->view->cities = ControllerUtils::mapItems(Services::createCategoryService()->findLocation(array("parentId" => 0,
                            "orders" => array(array("column" => "lft", "type" => "asc")))));
        $this->view->districts = ControllerUtils::mapItems(Services::createCategoryService()->findLocation(array("orders" => array(array("column" => "lft", "type" => "asc")))));
        $this->view->type = "create";
        $this->view->drugstoreId = '';
        $this->renderScript("drugstore/drugstore-cnu.phtml");
    }

    public function updateAction() {
        $params = $this->getRequest()->getParams();
        $drugstore = Services::createDrugstoreService()->get($params["id"]);
        $user = Services::createAuthenticationService()->getUser();

//        if (($user->getType() == SA_Entity_User::TYPE_MANAGER && $creator->getType() == SA_Entity_User::TYPE_ADMINISTRATOR)) {
//            $this->listAction();
//            return;
//        }

        if ($this->_request->isPost()) {
            $drugstore = $this->buildDrugstore($drugstore, $params);

//            if not exist
            if ($drugstore->getId() > 0) {
                Services::createDrugstoreService()->update($drugstore);
            } else {
                $drugstore->setId($params["id"]);
                Services::createDrugstoreService()->create($drugstore);
            }
            $this->view->result = true;
            $this->_redirect("/system/drugstore/list");
        }

        $this->view->drugstore = MapperUtil::mapObject(Services::createDrugstoreService()->get($params["id"]));
        $this->view->cities = MapperUtil::mapObjects(Services::createCategoryService()->findLocation(array("parentId" => 0,
                            "orders" => array(array("column" => "lft", "type" => "asc")))));
        $this->view->districts = MapperUtil::mapObjects(Services::createCategoryService()->findLocation(array("orders" => array(array("column" => "lft", "type" => "asc")))));
        $this->view->type = "update";
        $this->view->drugstoreId = $params["id"];
        $this->renderScript("drugstore/drugstore-cnu.phtml");
    }

    public function deleteAction() {
        $msg = ControllerUtils::validatePermision($this->view);

        if ($msg != "") {
            $this->renderScript("home/index.phtml");
            return;
        }
        $params = $this->getRequest()->getParams();
        Services::createDrugstoreService()->delete($params["id"]);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => true));
    }

    public function importDrugStoreAction() {
        
    }

    private function getFilterForListDrugstore($params) {
        $filter['pageSize'] = Services::createConfigurationService()->get("drugstore.system.page.size")->getValue();

        if (!isset($params['pageId'])) {
            $filter['pageId'] = 0;
        } else {
            $filter['pageId'] = intval($params['pageId']) - 1;
        }

        if (isset($params['address']) && $params['address'] != "") {
            $this->view->address = $params['address'];
            $filter['address'] = $params['address'];
        }

        if (isset($params['keyword']) && $params['keyword'] != "") {
            $this->view->keyword = $params['keyword'];
            $filter['name'] = $params['keyword'];
        }
        if (isset($params['cityId']) && $params['cityId'] != "0") {
            $this->view->cityId = $params['cityId'];
            $filter['cityId'] = $params['cityId'];

            if (isset($params['districtId']) && $params['districtId'] != "0") {
                $this->view->districtId = $params['districtId'];
                $filter['districtId'] = $params['districtId'];
            }
        }
        $filter["orders"] = array(array("column" => "order_number", "type" => "asc"));
        return $filter;
    }

    private function buildDrugstore(SA_Entity_Drugstore $drugstore, $params) {

        if (isset($params["cityId"])) {
            $drugstore->setCityId($params["cityId"]);
        }
        if (isset($params["districtId"])) {
            $drugstore->setDistrictId($params["districtId"]);
        }
        if (isset($params["description"])) {
            $drugstore->setDescription($params["description"]);
        }
        if (isset($params["name"])) {
            $drugstore->setName($params["name"]);
        }
        if (isset($params["address"])) {
            $drugstore->setAddress($params["address"]);
        }
        if (isset($params["status"])) {
            $drugstore->setStatus($params["status"]);
        }
        if (isset($params["email"])) {
            $drugstore->setEmail($params["email"]);
        }
        if (isset($params["phone"])) {
            $drugstore->setPhone($params["phone"]);
        }
        if (isset($params["imagePath"])) {
            $drugstore->setImagePath(($params["imagePath"] != "") ? $params["imagePath"] : Services::createConfigurationService()->get('icon.no.image.url')->getValue());
        }
        if (isset($params["orderNumber"])) {
            $drugstore->setOrderNumber(($params["orderNumber"] . length > 0) ? $params["orderNumber"] : (intval(Services::createDrugstoreService()->getMaxOrderNumber()) + 1));
        }
        $drugstore->setUpdateTime(new DateTime());

        if ($drugstore->getId() > 0) {
            
        } else {
            $drugstore->setCreateTime(new DateTime());
        }
        return $drugstore;
    }

}
