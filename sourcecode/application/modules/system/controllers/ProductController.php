<?php

class System_ProductController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->pathImg = "/img/uploads/";
    }

    public function indexAction() {
        // action body
    }

    public function listAction() {
        $params = $this->getRequest()->getParams();
        $productService = Services::createProductService();
        $categoryService = Services::createCategoryService();

        $pageSize = 10; // may get from configuration

        $this->view->advanceSearch = 'false';
        // prepare filter
        if (!isset($params['pageId'])) {
            $params['pageId'] = 0;
        } else {
            $params['pageId'] --;
        }


        $filter = array("pageSize" => $pageSize,
            "pageId" => $params["pageId"]);

        // search by keyword
        if (isset($params["keyword"]) && $params["keyword"] != "") {
            $filter["keyword"] = $params["keyword"];
            $this->view->keyword = $params["keyword"];
        }

        // conditions for many categories
        $categories = array();

        $filter["categoryIds"] = $categories;
        if (isset($params["categoryId"]) && $params["categoryId"] != "") {
            $categories[] = array("id" => $params["categoryId"], "type" => SA_Entity_Category::TYPE_GROUP_PRODUCT);
            $this->view->categoryId = $params["categoryId"];
            $this->view->advSearch = "true";
        }
        // get products
        $this->view->products = MapperUtil::mapObjects($productService->find($filter));

        // get count
        $filter ['metric'] = "record-count";
        $pageCount = $productService->find($filter);

        // get categories
        $filterCategory = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Category::TYPE_GROUP_PRODUCT,
            "orders" => array(array('column' => 'order_number', 'type' => 'asc')));
        $this->view->categories = MapperUtil::mapObjects($categoryService->find($filterCategory));


        //paginator        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
        $paginator = Zend_Paginator::factory(range(1, $pageCount));
        $paginator->setCurrentPageNumber(intval($filter["pageId"]) + 1);
        $paginator->setItemCountPerPage($pageSize);
        $this->view->paginator = $paginator;

        $this->renderScript("product/product-list.phtml");
    }

    public function createAction() {
        $service = Services::createProductService();
        $categoryService = Services::createCategoryService();

        //type
        $this->view->type = "create";

        // get categories
        $filterCategory = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Category::TYPE_GROUP_PRODUCT,
            "orders" => array(array('column' => 'order_number', 'type' => 'asc')));
        $this->view->categories = MapperUtil::mapObjects($categoryService->find($filterCategory));

        // return if direct access link
        if (!$this->getRequest()->isPost()) {
            $this->view->products = array();
            $this->view->categories = array();
            $this->view->product = array();
            $this->view->images = array();
            $this->renderScript("product/product-cnu.phtml");
            return;
        }

        $params = $this->getRequest()->getPost();
        $product = $this->paramToProduct($params, new SA_Entity_Product());
        $product->setCreateTime(new DateTime());

//        $product->setProductImages($this->createProductImage($params));

        try {
            $productMap = $service->create($product);

            // assign product to category
            $productId = $productMap['id'];
            if (isset($params['categoryIds']) && $params['categoryIds'] != "") {
                $categoryIds = explode(";", $params['categoryIds']);
                if (count($categoryIds) > 0) {
                    foreach ($categoryIds as $categoryId) {
                        $service->assignProductToCategory($productId, $categoryId);
                    }
                }
            }

//            // add related $product
//            if (isset($params['productIds']) && $params['productIds'] != "") {
//                $productIds = explode(";", $params['productIds']);
//                if (count($productIds) > 0) {
//                    foreach ($productIds as $relatedId) {
//                        $service->addRelation($productId, $relatedId);
//                    }
//                }
//            }

            $thumbnailPath = "";
            // add new product if exist
            if (isset($params["imageLinks"]) && $params["imageLinks"] != "") {
                $images = $this->createProductImage($params);
                if (count($images) > 0) {
                    foreach ($images as $img) {
                        $img->setProductId($productId);
                        $service->createProductImage($img);
                        if ($img->getIsRepresentation() != 0) {
                            $thumbnailPath = $img->getThumbnailUrl();
                        }
                    }
                }
            }

            if ($thumbnailPath != "") {
                $productMaps = $service->get($productId, null);
                foreach ($productMaps as $product) {
                    $product->setThumbnailUrl($thumbnailPath);
                    $service->update($product);
                }
            }

            $this->_redirect("/system/product/list?status=success");
        } catch (Exception $ex) {
            $this->_redirect("/system/product/create?status=error");
        }
    }

    public function updateAction() {
        $params = $this->getRequest()->getParams();
        $service = Services::createProductService();
        $categoryService = Services::createCategoryService();
        // prepare params
        $this->view->type = "update";

        // get categories
        $filterCategory = array("pageSize" => 0,
            "pageId" => 0,
            "type" => SA_Entity_Category::TYPE_GROUP_PRODUCT,
            "orders" => array(array('column' => 'order_number', 'type' => 'asc')));
        $this->view->categories = MapperUtil::mapObjects($categoryService->find($filterCategory));

        if (!$this->getRequest()->isPost()) {
            if (isset($params['id'])) {
                $products = $service->get($params['id'], NULL);
                if (count($products) > 0) {
                    $this->view->product = MapperUtil::mapObject($products[0]);
                } else {
                    $this->view->product = array();
                }

                // get selected categoryIds
                $filterCat = array("pageSize" => 0,
                    "pageId" => 0,
                    "type" => SA_Entity_Category::TYPE_GROUP_PRODUCT,
                    "productId" => $params['id']);
                $categories = MapperUtil::mapObjects($categoryService->find($filterCat));
                $this->view->selCategories = $categories;

                // get Images of product
                $filterImg = array("pageSize" => 0,
                    "pageId" => 0,
                    "productId" => $params['id']);
                $this->view->images = MapperUtil::mapObjects($service->findProductImages($filterImg));
            } else {
                $this->view->product = array();
            }

            // get
            $this->renderScript("product/product-cnu.phtml");
            return;
        }

        $id = $params["id"];

        // language
        $locale = new Zend_Locale();
        $lang = $locale->getLanguage();

        $products = $service->get($id, $lang);

        try {
            if (count($products) == 0) {
                $product = $this->paramToProduct($params, new SA_Entity_Product());
                $product->setCreateTime(new DateTime());
                $service->create($product);
            } else {
                $product = $this->paramToProduct($params, $products[0]);
                $product->setUpdateTime(new DateTime());
                $service->update($product);
            }

            $productId = $id;
            // update categories
            $service->unassignAllProductCategories($productId);
            if (isset($params['categoryIds']) && $params['categoryIds'] != "") {
                $categoryIds = explode(";", $params['categoryIds']);
                if (count($categoryIds) > 0) {
                    foreach ($categoryIds as $categoryId) {
                        $service->assignProductToCategory($productId, $categoryId);
                    }
                }
            }

            $thumbnailPath = $params["thumbnailUrl"];
            // if change thumbnail then update isRepresentation
            $filterImg = array("pageSize" => 0,
                "pageId" => 0,
                "productId" => $productId);
            $images = $service->findProductImages($filterImg);
            if (count($images) > 0) {
                foreach ($images as $img) {
                    if (strcmp($thumbnailPath, $img->getThumbnailUrl()) == 0) {
                        $img->setIsRepresentation(1);
                        $service->updateProductImage($img);
                    } else {
                        $img->setIsRepresentation(0);
                        $service->updateProductImage($img);
                    }
                }
            }

            // add new product if exist
            if (isset($params["imageLinks"]) && $params["imageLinks"] != "") {
                $images = $this->createProductImage($params);
                if (count($images) > 0) {
                    foreach ($images as $img) {
                        $img->setProductId($productId);
                        $service->createProductImage($img);
                        if ($img->getIsRepresentation() != 0) {
                            $thumbnailPath = $img->getThumbnailUrl();
                        }
                    }
                }
            }

            $this->_redirect("/system/product/update?id=" . $productId . "&status=success");
        } catch (Exception $ex) {
            $this->_redirect("/system/product/update?id=" . $productId . "&status=error");
        }
    }

    public function asyncDeleteAction() {
        $status = FALSE;
        $params = $this->getRequest()->getParams();
        $productService = Services::createProductService();
        try {
            if (key_exists("id", $params)) {
                $id = $params["id"];
                $productService->delete($id);
                $status = TRUE;
            }
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncDeleteProductImagesAction() {
        $params = $this->getRequest()->getParams();
        $productService = Services::createProductService();
        $params = $this->getRequest()->getParams();
        $status = FALSE;
        try {
            if (key_exists("screens", $params)) {
                $screens = $params["screens"];
                foreach ($screens as $screen) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $screen["url"]);
                    unlink($_SERVER['DOCUMENT_ROOT'] . $screen["thumbnailUrl"]);
                    $productService->deleteProductImage($screen["id"]);
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

    public function asyncListAction() {
        $params = $this->getRequest()->getParams();
        $productService = Services::createProductService();

        $filter = array("pageSize" => 0,
            "pageId" => 0);

        if (isset($params['keyword'])) {
            $filter["productName"] = $params['keyword'];
        } else {
            $filter["productName"] = "";
        }

        // get products
        $products = ControllerUtils::mapProducts($productService->find1($filter));

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("items" => $products));
    }

    public function asyncSlideImageAction() {
        $this->noRender();
        $album = array(
            array(
                "src" => "/img/test/tham-ghe.jpg"
            ),
            array(
                "src" => "/img/test/1.jpg"
            ),
            array(
                "src" => "/img/test/2.jpg"
            ),
            array(
                "src" => "/img/test/3.jpg"
            ),
            array(
                "src" => "/img/test/4.png"
            )
        );
        echo json_encode($album);
    }

    private function paramToProduct($param, $product) {
        if (isset($param["id"])) {
            /* @var $product DO_Entity_Product */
            $product->setId($param["id"]);
        }
        if (isset($param["name"])) {
            $product->setName($param["name"]);
        }
        if (isset($param["description"])) {
            $product->setDescription($param["description"]);
        }
        if (isset($param["code"])) {
            $product->setCode($param["code"]);
        }
        if (isset($param["status"])) {
            $product->setStatus($param["status"]);
        }
        if (isset($param["orderNumber"])) {
            $product->setOrderNumber($param["orderNumber"]);
        }

        if (isset($param["comment"])) {
            $product->setComment($param["comment"]);
        }
        if (isset($param["evaluation"])) {
            $product->setEvaluation($param["evaluation"]);
        }
        if (isset($param["feature"])) {
            $product->setFeature($param["feature"]);
        }
        if (isset($param["isShow"])) {
            $product->setIsShow($param["isShow"]);
        }
        if (isset($param["isNew"])) {
            $product->setIsNew($param["isNew"]);
        }
        if (isset($param["model"])) {
            $product->setModel($param["model"]);
        }
        if (isset($param["origin"])) {
            $product->setOrigin($param["origin"]);
        }
        if (isset($param["technique"])) {
            $product->setTechnique($param["technique"]);
        }
        if (isset($param["thumbnailUrl"])) {
            $product->setThumbnailUrl($param["thumbnailUrl"]);
        }

        $product->setUpdateTime(new DateTime());

        // language
        $locale = new Zend_Locale();
        $product->setLanguageCode($locale->getLanguage());

        return $product;
    }

    private function createProductImage($params) {
        $retVal = array();
        if (isset($params['imageLinks'])) {
            $imagesUrls = explode(";", $params['imageLinks']);
            foreach ($imagesUrls as $url) {
                $links = explode(",", $url);
                $image = new SA_Entity_ProductImage();
                $image->setCreateTime(new DateTime());
                $image->setName("");
                $image->setUrl($links[0]);
                $image->setThumbnailUrl($links[1]);
                if (isset($params['thumbnailUrl']) && strcmp($params['thumbnailUrl'], $links[0]) == 0) {
                    $image->setIsRepresentation(1);
                } else {
                    $image->setIsRepresentation(0);
                }
                $retVal[] = $image;
            }
        }
        return $retVal;
    }

}
