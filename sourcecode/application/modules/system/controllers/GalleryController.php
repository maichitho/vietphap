<?php

class System_GalleryController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        // action body
    }

    public function listAction() {
        $params = $this->getRequest()->getParams();
//        $params['pageSize'] = Services::createConfigurationService()->get("news.system.page.size")->getValue();

        $params['pageSize'] = 8;
        if (!isset($params['pageId'])) {
            $params['pageId'] = 0;
        } else {
            $params['pageId'] --;
        }

        if (isset($params['title']) && $params['title'] != "") {
            $this->view->title = $params['title'];
        } else {
            unset($params['title']);
        }

        $galleries = MapperUtil::mapObjects(Services::createGalleryService()->find($params));
        $params ['metric'] = "record-count";
        $pageCount = Services::createGalleryService()->find($params);
        $this->view->galleries = isset($galleries) ? $galleries : array();

        //paginator        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('components/pagination.phtml');
        $paginator = Zend_Paginator::factory(range(1, $pageCount));
        $paginator->setCurrentPageNumber($this->getPageNumber());
        $paginator->setItemCountPerPage($params['pageSize']);
        $this->view->paginator = $paginator;

        //Set search conditions
        $this->view->title = isset($this->view->title) ? $this->view->title : '';

        $this->renderScript("gallery/gallery-list.phtml");
    }

    public function createAction() {
        $gallery = new SA_Entity_Gallery();
        $gallery->setCreateTime(new DateTime())
                ->setUpdateTime(new DateTime())
                ->setTitle("Untitled Album")
                ->setIsTop('1');
        $id = Services::createGalleryService()->create($gallery);
        $gallery->setId($id);
        $this->view->gallery = MapperUtil::mapObject($gallery);
        $this->view->images = array();
        $this->renderScript("gallery/gallery-cnu.phtml");
    }

    public function viewAction() {
        $params = $this->getRequest()->getParams();
        $this->view->gallery = MapperUtil::mapObject(Services::createGalleryService()->get($params["id"]));
        $filter = array("galleryId" => $params["id"]);
        $this->view->images = MapperUtil::mapObjects(Services::createGalleryService()->findImage($filter));
        $this->renderScript("gallery/gallery-cnu.phtml");
    }

    public function asyncUpdateGalleryAction() {
        $params = $this->getRequest()->getParams();
        $galleryParam = $params["gallery"];
        $status = TRUE;
        try {
            $gallery = Services::createGalleryService()->get($galleryParam["id"]);
            Services::createGalleryService()->update($this->buildGallery($gallery, $galleryParam));
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncDeleteAction() {
        $params = $this->getRequest()->getParams();
        $id = $params["id"];
        $status = TRUE;
        try {
            Services::createGalleryService()->delete($id);
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncDeleteGalleryAction() {
        $params = $this->getRequest()->getParams();
        $id = $params["id"];
        $status = TRUE;
        try {
            Services::createGalleryService()->delete($id);
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncUpdateImageAction() {
        $params = $this->getRequest()->getParams();
        $image = $params["image"];
        $status = TRUE;
        try {
            $imageObj = Services::createGalleryService()->getImage($image["id"]);
            Services::createGalleryService()->updateImage($this->buildImage($imageObj, $params["image"]));
        } catch (Exception $ex) {
            $status = FALSE;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    /**
     * For deleting an images of a gallery
     */
    public function asyncDeleteImageAction() {
        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();

            $galleryService = Services::createGalleryService();

            $status = TRUE;
            $id = $params["id"];
            $url = $params["url"];
            try {
                unlink($_SERVER['DOCUMENT_ROOT'] . url);
                $galleryService->deleteImage($id);
                //send an email
            } catch (Exception $ex) {
                $status = FALSE;
            }
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode(array("status" => $status));
        }
    }

    /**
     * For create images of a gallery
     */
    public function asyncCreateImagesAction() {
        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();

            $galleryService = Services::createGalleryService();

            $status = TRUE;
            $galleryId = $params["id"];
            $images = $params["images"];

            $arrImages = array();
            try {
                foreach ($images as $image) {

                    $imageObj = new SA_Entity_Image();

                    // move file to user folder
                    $sourcePath = $_SERVER['DOCUMENT_ROOT'] . $image["url"];
                    $pathInfo = pathinfo($sourcePath);
                    $name = strtolower($pathInfo['filename']);
                    $ext = strtolower($pathInfo['extension']);

                    $imageObj->setName($image["name"])
                            ->setExtension($ext)
//                            ->setSize($image["size"])
                            ->setUrl($image["url"])
                            ->setThumbnailUrl($image["thumbnailUrl"])
                            ->setCreateTime(new DateTime())
                            ->setUpdateTime(new DateTime())
                            ->setType($image["type"])
                            ->setGalleryId($galleryId);

                    $imageId = $galleryService->createImage($imageObj);

                    $imageObj->setId($imageId);

                    $arrImages[] = $imageObj;
                    $status = TRUE;
                }

                // update thumbnail for gallery 
                $gallery = $galleryService->get($galleryId);
                if ($gallery->getThumbnailUrl() == null || $gallery->getThumbnailUrl() == "") {
                    $gallery->setUpdateTime(new DateTime())
                            ->setThumbnailUrl($arrImages[0]->getThumbnailUrl());
                    $galleryService->update($gallery);
                }

                //send an email
            } catch (Exception $ex) {
                $status = FALSE;
            }
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode(array("status" => $status,
                "images" => MapperUtil::mapObjects($arrImages)));
        }
    }

    private function buildGallery($gallery, $params) {
        $gallery->setCategoryId($params["categoryId"])
                ->setIsTop((int)$params["isTop"])
                ->setDescription($params["description"])
                ->setTitle($params["title"])
                ->setUpdateTime(new DateTime())
                ->setThumbnailUrl($params["thumbnailUrl"]);
        if ($gallery->getId() > 0) {
            
        } else {
            $gallery->setCreateTime(new DateTime());
        }
        return $gallery;
    }

    private function buildImage($image, $params) {
        $image->setGalleryId($params["galleryId"])
                ->setName($params["name"])
                ->setDescription($params["description"])
                ->setTitle($params["title"])
                ->setUpdateTime(new DateTime())
                ->setThumbnailUrl($params["thumbnailUrl"])
                ->setType($params["type"])
                ->setUrl($params["url"])
                ->setExtension($params["extension"]);
        if ($image->getId() > 0) {
            
        } else {
            $image->setCreateTime(new DateTime());
        }
        return $image;
    }

}
