<?php

/**
 * Description of FileUtils
 *
 * @author Sililab
 */
class FileUtil {

    /**
     * Get hash value from file name
     * 
     * @param type $name
     * @return hash string
     */
    public static function hashFileName($name) {
        return substr(md5(uniqid(mt_rand() . $name, true)), 0, 4);
    }

    function unique_id($l = 8) {
        
    }

    public static function resizeImage($sourceImagePath, $targetDirectoryPath, $width, $heigth) {
        $filePath = "";
        try {
            $filter = new Zend_Filter_ImageSize();
            $filePath = $filter->setThumnailDirectory($targetDirectoryPath)
                    ->setWidth($width)
                    ->setHeight($heigth)
                    ->filter($sourceImagePath);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $filePath;
    }

    /**
     * Create folder structure basing on filename
     *  
     * @param type $filename
     * @return relative folder path
     */
    public static function createFolderByFileName($serverPath, $filename) {

        // prepare folder structure
        $retVal = array();

        $relativeFolder = substr($filename, 0, 2) . "/" . substr($filename, 2, 2) . "/" . substr($filename, 4, 2) . "/";

        $folder = $serverPath . "/usrc/" . $relativeFolder;
        $thumbnailFolder = $folder . "thumbnail/";

        if (!file_exists($thumbnailFolder) || !is_dir($thumbnailFolder)) {
            $reVal = mkdir($thumbnailFolder, 0755, true);
        }

        $retVal["path"] = $folder;
        $retVal["thumbnailPath"] = $thumbnailFolder;
        $retVal["relativePath"] = "/usrc/" . $relativeFolder;

        if ($reVal) {
            return $retVal;
        } else {
            return "";
        }
    }

    public static function moveFileToUserFolder($url) {
        $retVal = array();
        // move file to user folder
        $sourcePath = $_SERVER['DOCUMENT_ROOT'] . $url;
        $pathInfo = pathinfo($sourcePath);
        $name = strtolower($pathInfo['filename']);
        $ext = strtolower($pathInfo['extension']);

        // create folder for target path
        $targetPath = FileUtil::createFolderByFileName($name);

        if ($targetPath != "") {
            $targetOriginPath = $targetPath . $name . "_o" . "." . $ext;
            $targetThumbPath = $targetPath . '_t/';

            // move to user's folder
            rename($sourcePath, $targetOriginPath);

            // create thumbnail
            FileUtil::resizeImage($targetOriginPath, $targetThumbPath, 200, 200);

            $relativePath = substr($name, 0, 2) . "/" . substr($name, 2, 2) . "/" . substr($name, 4, 2) . "/";
            $retVal["url"] = "/usrc/" . $relativePath . $name . "_o" . "." . $ext;
            $retVal["url_thumbnail"] = "/usrc/" . $relativePath . "_t/" . $name . "_o" . "." . $ext;
        }
        return $retVal;
    }

    /**
     * Get new hash name of file
     * 
     * @param type $originName
     * @return type
     */
    public static function createHashFileName($originName) {
        $userId = Services::createAuthenticationService()->getUser()->getId();
//        $userId = 1;
        $pathInfo = pathinfo($originName);
        $fileExtionsion = strtolower($pathInfo['extension']);

        return FileUtil::hashFileName($originName) . "_" . $originName;
    }

}
