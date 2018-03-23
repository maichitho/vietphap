<?php

class System_FileController extends Zend_Controller_Action {

    public function init() {
        $this->targetPath = $_SERVER['DOCUMENT_ROOT'] . '/img/uploads/';
        $this->relativePath = "/img/uploads/";
        $this->thumbnailPath = $_SERVER['DOCUMENT_ROOT'] . '/img/thumbnails/';

        if (!file_exists($this->targetPath) || !is_dir($this->targetPath)) {
            mkdir($this->targetPath, 0777);
        }
        if (!file_exists($this->thumbnailPath) || !is_dir($this->thumbnailPath)) {
            mkdir($this->thumbnailPath, 0777);
        }

        $this->targetFilePath = $_SERVER['DOCUMENT_ROOT'] . '/files/uploads/';
        $this->relativeFilePath = "/files/uploads/";

        if (!file_exists($this->targetFilePath) || !is_dir($this->targetFilePath)) {
            mkdir($this->targetFilePath, 0777);
        }
    }

    public function getFileAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $formData = $this->getRequest()->getParams();
        $invalidFile = true;
        if (key_exists("path", $formData)) {
            $path = urldecode($formData['path']);
            $invalidFile = false;
        }
        if (!$invalidFile) {
            $file = file_get_contents($path);
            if ($file === false) {
                $invalidFile = true;
            } else {
                $this->getResponse()->clearBody();
                $this->getResponse()->setHeader('Content-Type', '');
                $this->getResponse()->setBody($file);
            }
        }
    }

    public function deleteFileAction() {
        $status = FALSE;
        $formData = $this->getRequest()->getParams();
        if (key_exists("filePath", $formData)) {
            $status = unlink($_SERVER['DOCUMENT_ROOT'] . $formData["filePath"]);
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncDeleteFileAction() {
        $status = FALSE;
        $formData = $this->getRequest()->getParams();
        $supplierService = Services::createSupplierService();
        $supplier = $supplierService->get($formData['id']);
        try {
            if ($supplier != null) {
                if (isset($formData['fileType'])) {
                    if (strcmp("imagePath", $formData['fileType']) == 0) {
                        $supplier->setImagePath("");
                    } else if (strcmp("logoPath", $formData['fileType']) == 0) {
                        $supplier->setLogoPath("");
                    } else if (strcmp("contractFilePath", $formData['fileType']) == 0) {
                        $supplier->setContractFilePath("");
                    }
                }
                $supplierService->update($supplier);
            }
            $status = unlink($_SERVER['DOCUMENT_ROOT'] . $formData["filePath"]);
        } catch (Exception $ex) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode(array("status" => $status));
        }

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    public function asyncUploadFileAction() {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();
            $uploadFolder = $this->targetPath;
            //echo $upload_folder;
            $adapter->setDestination($uploadFolder);
            //echo $_FILES['Filedata']['tmp_name'];
            if (!empty($_FILES)) {
                $tempFile = $_FILES['Filedata']['tmp_name'];
            }
            $oldName = $adapter->getFileName('Filedata', false);
            $pathInfo = pathinfo($oldName);
            $fileExtionsion = strtolower($pathInfo['extension']);
            $newName = time() . '.' . $fileExtionsion; //. $oldName;
            $filePath = $uploadFolder . DIRECTORY_SEPARATOR . $newName;
            $adapter->addFilter('Rename', array('target' => $filePath,
                'overwrite' => true));
            $adapter->receive();
            //response:
            //response:
            $status = TRUE;
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode(array("status" => $status,
                "filePath" => $this->relavitePath . $newName,
                "fileName" => $newName));
        }
    }

    public function asyncUploadFileDocAction() {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        if ($this->_request->isPost()) {
            $params = $this->getRequest()->getParams();
            $uploadFolder = $this->targetFilePath;
            //echo $upload_folder;
            $adapter->setDestination($uploadFolder);
            //echo $_FILES['Filedata']['tmp_name'];
            if (!empty($_FILES)) {
                $tempFile = $_FILES['Filedata']['tmp_name'];
            }
            $oldName = $adapter->getFileName('Filedata', false);
            $pathInfo = pathinfo($oldName);
            $fileExtionsion = strtolower($pathInfo['extension']);
            $newName = time() . '.' . $fileExtionsion; //. $oldName;
            $filePath = $uploadFolder . DIRECTORY_SEPARATOR . $newName;
            $adapter->addFilter('Rename', array('target' => $filePath,
                'overwrite' => true));
            $adapter->receive();
            //response:
            //response:
            $status = TRUE;
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode(array("status" => $status,
                "filePath" => $this->relaviteFilePath . $newName,
                "fileName" => $newName));
        }
    }

    public function asyncUploadMultiFileAction() {
        $adapter = new Zend_File_Transfer_Adapter_Http();
//        $user = Services::createAuthenticationService()->getUser();
//        $asd = new SA_Entity_User();


        if (!empty($_FILES)) {  // Process uploaded file
            $upload_dir = $this->targetPath;  // Set upload path WITH TRAILING SLASH
            $upload_thumb_dir = $this->thumbnailPath;

            $file_temp = $_FILES['Filedata']['tmp_name'];   // Retrieve temporary file path
            $file_orig = basename($_FILES['Filedata']['name']);  // Retrieve original file name

            $file_save = time() . $file_orig;   // Prepend filename with time stamp to avoid overwriting files
            $target_path = $upload_dir . $file_save; // Prepand filename with the upload path
            // optimize file temp
            // move to thumbnail
            // move origin to target

            if (move_uploaded_file($file_temp, $target_path)) {
                $status = TRUE;
//                $reVal = Util::resizeImage($target_path, $upload_thumb_dir, 220, null);
            } else {
                $status = FALSE;
            }

            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode(array("status" => $status,
                "filePath" => $this->relativePath . $file_save,
                "fileName" => $file_save));
        }
    }

    /**
     * Using for new upload library
     */
    public function asyncUploadAction() {
        $status = FALSE;
        try {
            $upload_handler = new UploadHandler();
            $status = TRUE;
        } catch (Exception $ex) {
            $status = FALSE;
        }

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//        echo json_encode(array("status" => $status));
    }

    public function asyncDeleteFilesAction() {
        $status = FALSE;
        $params = $this->getRequest()->getParams();
        if (key_exists("screens", $params)) {
            $screens = $params["screens"];
            foreach ($screens as $screen) {
                $status = unlink($_SERVER['DOCUMENT_ROOT'] . $screen["url"]);
                $status = unlink($_SERVER['DOCUMENT_ROOT'] . $screen["thumbnailUrl"]);
            }
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo json_encode(array("status" => $status));
    }

    function backup_tables($host, $user, $pass, $name, $tables = '*') {
        $con = mysql_connect($host, $user, $pass);
        mysql_select_db($name, $con);

//get all of the tables
        if ($tables == '*') {
            $tables = array();
            $result = mysql_query('SHOW TABLES');
            while ($row = mysql_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        $return = "";

//cycle through
        foreach ($tables as $table) {
            $result = mysql_query('SELECT * FROM ' . $table);
            $num_fields = mysql_num_fields($result);
            $return.= 'DROP TABLE ' . $table . ';';
            $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
            $return.= "nn" . $row2[1] . ";nn";

            while ($row = mysql_fetch_row($result)) {
                $return.= 'INSERT INTO ' . $table . ' VALUES(';
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace("#n#", "n", $row[$j]);
                    if (isset($row[$j])) {
                        $return.= '"' . $row[$j] . '"';
                    } else {
                        $return.= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return.= ',';
                    }
                }
                $return.= ");n";
            }
            $return.="nnn";
        }

//save file
        $handle = fopen('db-backup-' . time() . '-' . (md5(implode(',', $tables))) . '.sql', 'w+');
        fwrite($handle, $return);
        fclose($handle);
    }

    public function backupAction() {
        ini_set("max_execution_time", 0);

        $dir = "fobic";
        if (!(file_exists($dir))) {
            mkdir($dir, 0777);
        }
        $zip = new ZipArchive();
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $params = $config->getOption('resources');

        $this->backup_tables($params["db"]["params"]["host"], $params["db"]["params"]["username"], $params["db"]["params"]["password"], $params["db"]["params"]["dbname"]);
        if (glob("*.sql") != false) {
            $filecount = count(glob("*.sql"));
            $arr_file = glob("*.sql");

            for ($j = 0; $j < $filecount; $j++) {
                $res = $zip->open($arr_file[$j] . ".zip", ZipArchive::CREATE);
                if ($res === TRUE) {
                    $zip->addFile($arr_file[$j]);
                    $zip->close();
                    unlink($arr_file[$j]);
                }
            }
        }
        //get the current folder name-start
        $path = dirname($_SERVER['PHP_SELF']);
        $position = strrpos($path, '/') + 1;
        $folder_name = substr($path, $position);
//get the current folder name-end
        $zipname = date('Y/m/d');
        $str = "stark-" . $zipname . ".zip";
        $str = str_replace("/", "-", $str);
//// open archive
        if ($zip->open($str, ZIPARCHIVE::CREATE) !== TRUE) {
            die("Could not open archive");
        }
//// initialize an iterator
//// pass it the directory to be processed
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../$folder_name/"));
//// iterate over the directory
//// add each file found to the archive
////
//        foreach ($iterator as $key => $value) {
//            if (strstr(realpath($key), "usrc") == FALSE) {
//                $zip->addFile(realpath($key), $key) or die("ERROR: Could not add file: $key");
//            }
//        }
//// close and save archive
        $zip->close();
//        
//        echo "Archive created successfully.";
//        if (glob("*.sql.zip") != false) {
//            $filecount = count(glob("*.sql.zip"));
//            $arr_file = glob("*.sql.zip");
//
//            for ($j = 0; $j < $filecount; $j++) {
//                unlink($arr_file[$j]);
//            }
//        }
//get the array of zip files
        if (glob("*.zip") != false) {
            $arr_zip = glob("*.zip");
        }
//
//copy the backup zip file to site-backup-stark folder
        foreach ($arr_zip as $key => $value) {
            if (strstr($value, "db-backup")) {
                $delete_zip[] = $value;
                copy("$value", "$dir/$value");
            }
        }
        for ($i = 0; $i < count($delete_zip); $i++) {
            unlink($delete_zip[$i]);
        }
        $this->renderScript("components/dialog-popup_success.phtml");
    }

}
