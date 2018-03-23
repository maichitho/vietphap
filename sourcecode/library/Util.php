<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GbaUtil
 *
 * @author Sililab
 */
class Util {

    const FROM_TIME_TYPE = 'from_time';
    const TO_TIME_TYPE = 'to_time';

    public static $DEFAULT_LOCALE = 'vi_VN';
    public static $DEFAULT_DATE_FORMAT = 'dd/mm/yyyy';
    public static $DATE_FORMAT = 'd/m/Y';
    public static $VIETNAM_DATE = 'vn';
    public static $DATE_TIMF_FORMAT = 'h:ia d/m/y';
    private static $SPECIAL_CHARS = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ"
        , "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ",
        "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
        , "ờ", "ớ", "ợ", "ở", "ỡ",
        "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
        "ỳ", "ý", "ỵ", "ỷ", "ỹ",
        "đ",
        "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
        , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
        "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
        "Ì", "Í", "Ị", "Ỉ", "Ĩ",
        "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
        , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
        "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
        "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
        "Đ", "ê", "ù", "à",
        "\\", "/", ",");
    private static $NORMAL_CHARS = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
        , "a", "a", "a", "a", "a", "a",
        "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
        "i", "i", "i", "i", "i",
        "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
        , "o", "o", "o", "o", "o",
        "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
        "y", "y", "y", "y", "y",
        "d",
        "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
        , "A", "A", "A", "A", "A",
        "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
        "I", "I", "I", "I", "I",
        "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
        , "O", "O", "O", "O", "O",
        "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
        "Y", "Y", "Y", "Y", "Y",
        "D", "e", "u", "a",
        "", "-", "-");

    /**
     * SQL format: Y:m:d H:i:s
     * @return type
     */
    public static function now() {
        return date("Y:m:d H:i:s");
    }

    public static function formatDateTime($dateTimeString) {
        if ($dateTimeString != null) {
            //$date = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //$date = $this -> createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //$dateString = split
            //mktime
            $explodes = explode(' ', $dateTimeString);
            $dates = explode('-', $explodes[0]);
            return $dates[2] . '/' . $dates[1] . '/' . $dates[0] . ' ' . $explodes[1]; //FORMAT: 'd/m/Y H:i:s'
        } else {
            return '';
        }
    }

    public static function formatTime($dateTimeString) {
        if ($dateTimeString != null) {
            //$date = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //$date = $this -> createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //return $date->format('H:i:s');
            $explodes = explode(' ', $dateTimeString);
            return $explodes[1];
        } else {
            return '';
        }
    }

    /*
     * Convert dateString from Y-m-d H:i:s to H:i AM/PM d/m/Y
     */

    public static function formatDateTimeByCountry($dateTimeString, $country) {
        if ($country == GbaUtil::$VIETNAM_DATE && $dateTimeString != null) {
            $explodes = explode(' ', $dateTimeString);
            $dates = explode('-', $explodes[0]);
            $times = explode(':', $explodes[1]);
            return $times[0] . ':' . $times[1] . ' ' . $dates[2] . '/' . $dates[1] . '/' . $dates[0];
        } else {
            return '';
        }
    }

    /**
     * Convert datetime from Y:m:d H:i:s to Y-m-d H:i:s
     * @param type $dateTimeString
     * @return string 
     */
    public static function formatDateToStandard($dateTimeString) {
        if ($dateTimeString != null) {
            //$date = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //$date = $this -> createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //$dateString = split
            //mktime
            $explodes = explode(' ', $dateTimeString);
            $dates = explode(':', $explodes[0]);
            return $dates[2] . '-' . $dates[1] . '-' . $dates[0] . ' ' . $explodes[1]; //FORMAT: 'd/m/Y H:i:s'
        } else {
            return '';
        }
    }

    /**
     * Convert datetime to name (TODAY, YESTERDAY)
     * @param type $dateTimeString
     * @return string 
     */
    public static function formatDateToName($dateTimeString) {
        if ($dateTimeString != null) {
            $dateNow = date('Y-m-d');
            $dateNows = explode('-', $dateNow);

            $explodes = explode(' ', $dateTimeString);
            $dates = explode('-', $explodes[0]);

            if ($dates[0] == $dateNows[0] && $dates[1] == $dateNows[1] && $dates[2] == $dateNows[2]) {
                return 'TODAY';
            } else {
                return $dates[2] . '/' . $dates[1] . '/' . $dates[0]; //FORMAT: 'd/m/Y H:i:s'
            }
        } else {
            return '';
        }
    }

    /**
     * convert date string with format Y-m-d to vietnamese format d/m/Y string
     * @param type $dateTimeString
     * @return string
     */
    public static function formatDate($dateTimeString, $withoutYear = false) {
        if ($dateTimeString != null) {
            //$date = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
            //return $date->format('d/m/Y');
            $explodes = explode(' ', $dateTimeString);
            //echo '_____'.$explodes[0];
            $dates = explode('-', $explodes[0]);
            if (count($dates) != 3) {
                $dates = explode(':', $explodes[0]);
            }
            if ($withoutYear) {
                return $dates[2] . '/' . $dates[1];
            } else {
                return $dates[2] . '/' . $dates[1] . '/' . $dates[0];
            }
        } else {
            return '';
        }
    }

    /**
     * from vietnamese format (d/m/Y H:i:s) to SQL format (Y:m:d H:i:s)
     * @param type $dateTimeString
     * @return type
     */
    public static function toSQLDateTimeString($dateTimeString) {
        $explodes = explode(' ', $dateTimeString);
        $datePart = '0:0:0';
        $timePart = '0:0:0';
        if (count($explodes) >= 1) {
            //$datePart = str_replace('/', ':', $explodes[0]);
            $tempExplodes = explode('/', $explodes[0]);
            if (count($tempExplodes) == 3) {
                $datePart = $tempExplodes[2] . ':' . $tempExplodes[1] . ':' . $tempExplodes[0];
            }
        }
        if (count($explodes) == 2) {
            $timePart = $explodes[1];
        }
        //return
        return $datePart . ' ' . $timePart;
    }

    public static function toSQLDateTimeStringWithFromTo($dateTimeString, $type) {
        $explodes = explode(' ', $dateTimeString);
        $datePart = '0:0:0';
        $timePart = ($type == Util::FROM_TIME_TYPE) ? '0:0:0' : '23:29:29';
        if (count($explodes) >= 1) {
            //$datePart = str_replace('/', ':', $explodes[0]);
            $tempExplodes = explode('/', $explodes[0]);
            if (count($tempExplodes) == 3) {
                $datePart = $tempExplodes[2] . ':' . $tempExplodes[1] . ':' . $tempExplodes[0];
            }
        }
        if (count($explodes) == 2) {
            $timePart = $explodes[1];
        }
        //return
        return $datePart . ' ' . $timePart;
    }

    public static function toFriendlyString($string) {
        if ($string != NULL) {
            $asciiString = str_replace(Util::$SPECIAL_CHARS, Util::$NORMAL_CHARS, $string);
            $stdString = preg_replace('/\s\s+/', ' ', $asciiString);
            $retVal = strtolower(str_replace(' ', '-', $stdString));
            //return
            return $retVal;
        } else {
            return "";
        }
    }

    /**
     * 
     * @param type $dateTimeString: Vietnamese format: d/m/Y H:i:s
     */
    public static function toTimestamp($dateTimeString) {
        return strtotime(GbaUtil::toSQLDateTimeString($dateTimeString));
    }

    public static function recordsCountToPagesCount($recordsCount, $pageSize) {
        $retVal = (int) ($recordsCount / $pageSize);
        if ($recordsCount % $pageSize > 0) {
            $retVal++;
        }
        //return
        return $retVal;
    }

    public static function moneyToString($moneyInInt) {
        return number_format($moneyInInt, 0, ',', '.');
    }

    public static function extensionCodeToString($extensionCode, $colors) {
        $size = substr($extensionCode, 0, 2);
        if (is_numeric($size)) {
            $shortColorCode = substr($extensionCode, 2);
            foreach ($colors as $key => $value) {
                if (strpos($key, $shortColorCode) === 0) {
                    $color = $value;
                    break;
                }
            }
        }
        return $size . ' / ' . ( isset($color) ? $color : '(Không xác định)' );
    }

    public static function setLocale($locale) {
        Services::createAuthenticationService()->getSession()->locale = $locale;
    }

    public static function getLocale() {
        try {
            $locale = Services::createAuthenticationService()->getSession()->locale;
            if (!isset($locale) || $locale == null) {
                $locale = Util::$DEFAULT_LOCALE;
            }
        } catch (Exception $exc) {
            $locale = Util::$DEFAULT_LOCALE;
            //echo $exc->getTraceAsString();
        }


        return $locale;
    }

    public static function translate($string, $params = array()) {
//        $locale = Services::createConfigurationService()->getParam( 'system.language' )->getValue();

        $translate = Zend_Registry::get('Zend_Translate');
        if ($translate == null) {
            $locale = new Zend_Locale('vi-VN');
            Zend_Registry::set('Zend_Locale', $locale);

//        $cache = Zend_Cache::factory('Core', 'File', null, null);
//        Zend_Translate::setCache($cache);
            $translate = new Zend_Translate(array(
                'adapter' => 'ini',
                'content' => APPLICATION_PATH . '/../language/vi_VN.ini',
                'locale' => 'vi-VN'
            ));

            $translate->addTranslation(array(
                'adapter' => 'ini',
                'content' => APPLICATION_PATH . '/../language/en_US.ini',
                'locale' => 'en-US'
            ));

            if ($translate->isAvailable($locale)) {
                $translate->setLocale($locale);
            }

            Zend_Registry::set('Zend_Translate', $translate);
        }

        $retval = $translate->_($string);
        foreach ($params as $paramKey => $paramValue) {
            $retval = str_replace('{' . $paramKey . '}', $paramValue, $retval);
        }
        return $retval;
    }

    public static function money_format($format, $number) {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
                '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }

        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                        $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                        $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int) $fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int) $fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int) $fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                        $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep'] );
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                                STR_PAD_RIGHT : STR_PAD_LEFT );
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
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

    public static function sendMail($to, $receiverName, $subject, $body, $bcc = false) {
        $retval = TRUE;
        $configService = Services::createConfigurationService();
        try {
            $param = $configService->get("system.email");
            if ($param != NULL) {
                $emailConfig = json_decode($param->getValue(), true);
                $retval = TRUE;

//                $config = array('host' => 'mail.vnbuyers.com',
                $config = array('host' => $configService->get('system.email.config.host')->getValue(),
                    'port' => $configService->get('system.email.config.port')->getValue(),
                    'ssl' => $configService->get('system.email.config.ssl')->getValue(),
                    'auth' => $configService->get('system.email.config.auth')->getValue(),
                    'username' => $emailConfig["email"],
                    'password' => $emailConfig["password"]);
//                $transport = new Zend_Mail_Transport_Smtp('mail.vnbuyers.com', $config);
                $transport = new Zend_Mail_Transport_Smtp($configService->get('system.email.config.smtp')->getValue(), $config);
                $mail = new Zend_Mail('UTF-8');
                $mail->setBodyHtml($body);
                $mail->setFrom('contact@vnbuyers.com', Util::translate("email.from.name"));
                $mail->addTo($to, $receiverName);
                if ($bcc)
                    $mail->addBcc($emailConfig["email"]);
                $mail->setSubject($subject);
                $mail->send($transport);
            } else {
                $retval = FALSE;
            }
        } catch (Exception $exc) {
            $retval = FALSE;
        }
        return $retval;
    }

    public static function getDateFormat($locale) {
        if ($locale == "vi") {
            return "dd/mm/yy";
        }
    }

    /*
     * Function for Create/Update/Remove selected Products 
     */

    public static function setProductInCart($product) {
        Services::createAuthenticationService()->getSession()->productList[] = $product;
    }

    public static function setListProductInCart($list) {
        Services::createAuthenticationService()->getSession()->productList = $list;
    }

    public static function getProductInCart() {
        try {
            $list = Services::createAuthenticationService()->getSession()->productList;
            if (!isset($list) || $list == null) {
                $list = array();
            }
        } catch (Exception $exc) {
            $list = array();
            //echo $exc->getTraceAsString();
        }

        return $list;
    }

    /*
     * Function for Create/Update Receiver Information
     */

    public static function setReceiverInformation($info) {
        Services::createAuthenticationService()->getSession()->receiverInformation = $info;
    }

    public static function getReceiverInformation() {
        try {
            $info = Services::createAuthenticationService()->getSession()->receiverInformation;
            if (!isset($info) || $info == null) {
                $info = array();
            }
        } catch (Exception $exc) {
            $info = array();
            //echo $exc->getTraceAsString();
        }

        return $info;
    }

    public static function formatcurrency($floatcurr, $curr = "USD") {
        $currencies['ARS'] = array(2, ',', '.');          //  Argentine Peso
        $currencies['AMD'] = array(2, '.', ',');          //  Armenian Dram
        $currencies['AWG'] = array(2, '.', ',');          //  Aruban Guilder
        $currencies['AUD'] = array(2, '.', ' ');          //  Australian Dollar
        $currencies['BSD'] = array(2, '.', ',');          //  Bahamian Dollar
        $currencies['BHD'] = array(3, '.', ',');          //  Bahraini Dinar
        $currencies['BDT'] = array(2, '.', ',');          //  Bangladesh, Taka
        $currencies['BZD'] = array(2, '.', ',');          //  Belize Dollar
        $currencies['BMD'] = array(2, '.', ',');          //  Bermudian Dollar
        $currencies['BOB'] = array(2, '.', ',');          //  Bolivia, Boliviano
        $currencies['BAM'] = array(2, '.', ',');          //  Bosnia and Herzegovina, Convertible Marks
        $currencies['BWP'] = array(2, '.', ',');          //  Botswana, Pula
        $currencies['BRL'] = array(2, ',', '.');          //  Brazilian Real
        $currencies['BND'] = array(2, '.', ',');          //  Brunei Dollar
        $currencies['CAD'] = array(2, '.', ',');          //  Canadian Dollar
        $currencies['KYD'] = array(2, '.', ',');          //  Cayman Islands Dollar
        $currencies['CLP'] = array(0, '', '.');           //  Chilean Peso
        $currencies['CNY'] = array(2, '.', ',');          //  China Yuan Renminbi
        $currencies['COP'] = array(2, ',', '.');          //  Colombian Peso
        $currencies['CRC'] = array(2, ',', '.');          //  Costa Rican Colon
        $currencies['HRK'] = array(2, ',', '.');          //  Croatian Kuna
        $currencies['CUC'] = array(2, '.', ',');          //  Cuban Convertible Peso
        $currencies['CUP'] = array(2, '.', ',');          //  Cuban Peso
        $currencies['CYP'] = array(2, '.', ',');          //  Cyprus Pound
        $currencies['CZK'] = array(2, '.', ',');          //  Czech Koruna
        $currencies['DKK'] = array(2, ',', '.');          //  Danish Krone
        $currencies['DOP'] = array(2, '.', ',');          //  Dominican Peso
        $currencies['XCD'] = array(2, '.', ',');          //  East Caribbean Dollar
        $currencies['EGP'] = array(2, '.', ',');          //  Egyptian Pound
        $currencies['SVC'] = array(2, '.', ',');          //  El Salvador Colon
        $currencies['ATS'] = array(2, ',', '.');          //  Euro
        $currencies['BEF'] = array(2, ',', '.');          //  Euro
        $currencies['DEM'] = array(2, ',', '.');          //  Euro
        $currencies['EEK'] = array(2, ',', '.');          //  Euro
        $currencies['ESP'] = array(2, ',', '.');          //  Euro
        $currencies['EUR'] = array(2, ',', '.');          //  Euro
        $currencies['FIM'] = array(2, ',', '.');          //  Euro
        $currencies['FRF'] = array(2, ',', '.');          //  Euro
        $currencies['GRD'] = array(2, ',', '.');          //  Euro
        $currencies['IEP'] = array(2, ',', '.');          //  Euro
        $currencies['ITL'] = array(2, ',', '.');          //  Euro
        $currencies['LUF'] = array(2, ',', '.');          //  Euro
        $currencies['NLG'] = array(2, ',', '.');          //  Euro
        $currencies['PTE'] = array(2, ',', '.');          //  Euro
        $currencies['GHC'] = array(2, '.', ',');          //  Ghana, Cedi
        $currencies['GIP'] = array(2, '.', ',');          //  Gibraltar Pound
        $currencies['GTQ'] = array(2, '.', ',');          //  Guatemala, Quetzal
        $currencies['HNL'] = array(2, '.', ',');          //  Honduras, Lempira
        $currencies['HKD'] = array(2, '.', ',');          //  Hong Kong Dollar
        $currencies['HUF'] = array(0, '', '.');           //  Hungary, Forint
        $currencies['ISK'] = array(0, '', '.');           //  Iceland Krona
        $currencies['INR'] = array(2, '.', ',');          //  Indian Rupee
        $currencies['IDR'] = array(2, ',', '.');          //  Indonesia, Rupiah
        $currencies['IRR'] = array(2, '.', ',');          //  Iranian Rial
        $currencies['JMD'] = array(2, '.', ',');          //  Jamaican Dollar
        $currencies['JPY'] = array(0, '', ',');           //  Japan, Yen
        $currencies['JOD'] = array(3, '.', ',');          //  Jordanian Dinar
        $currencies['KES'] = array(2, '.', ',');          //  Kenyan Shilling
        $currencies['KWD'] = array(3, '.', ',');          //  Kuwaiti Dinar
        $currencies['LVL'] = array(2, '.', ',');          //  Latvian Lats
        $currencies['LBP'] = array(0, '', ' ');           //  Lebanese Pound
        $currencies['LTL'] = array(2, ',', ' ');          //  Lithuanian Litas
        $currencies['MKD'] = array(2, '.', ',');          //  Macedonia, Denar
        $currencies['MYR'] = array(2, '.', ',');          //  Malaysian Ringgit
        $currencies['MTL'] = array(2, '.', ',');          //  Maltese Lira
        $currencies['MUR'] = array(0, '', ',');           //  Mauritius Rupee
        $currencies['MXN'] = array(2, '.', ',');          //  Mexican Peso
        $currencies['MZM'] = array(2, ',', '.');          //  Mozambique Metical
        $currencies['NPR'] = array(2, '.', ',');          //  Nepalese Rupee
        $currencies['ANG'] = array(2, '.', ',');          //  Netherlands Antillian Guilder
        $currencies['ILS'] = array(2, '.', ',');          //  New Israeli Shekel
        $currencies['TRY'] = array(2, '.', ',');          //  New Turkish Lira
        $currencies['NZD'] = array(2, '.', ',');          //  New Zealand Dollar
        $currencies['NOK'] = array(2, ',', '.');          //  Norwegian Krone
        $currencies['PKR'] = array(2, '.', ',');          //  Pakistan Rupee
        $currencies['PEN'] = array(2, '.', ',');          //  Peru, Nuevo Sol
        $currencies['UYU'] = array(2, ',', '.');          //  Peso Uruguayo
        $currencies['PHP'] = array(2, '.', ',');          //  Philippine Peso
        $currencies['PLN'] = array(2, '.', ' ');          //  Poland, Zloty
        $currencies['GBP'] = array(2, '.', ',');          //  Pound Sterling
        $currencies['OMR'] = array(3, '.', ',');          //  Rial Omani
        $currencies['RON'] = array(2, ',', '.');          //  Romania, New Leu
        $currencies['ROL'] = array(2, ',', '.');          //  Romania, Old Leu
        $currencies['RUB'] = array(2, ',', '.');          //  Russian Ruble
        $currencies['SAR'] = array(2, '.', ',');          //  Saudi Riyal
        $currencies['SGD'] = array(2, '.', ',');          //  Singapore Dollar
        $currencies['SKK'] = array(2, ',', ' ');          //  Slovak Koruna
        $currencies['SIT'] = array(2, ',', '.');          //  Slovenia, Tolar
        $currencies['ZAR'] = array(2, '.', ' ');          //  South Africa, Rand
        $currencies['KRW'] = array(0, '', ',');           //  South Korea, Won
        $currencies['SZL'] = array(2, '.', ', ');         //  Swaziland, Lilangeni
        $currencies['SEK'] = array(2, ',', '.');          //  Swedish Krona
        $currencies['CHF'] = array(2, '.', '\'');         //  Swiss Franc 
        $currencies['TZS'] = array(2, '.', ',');          //  Tanzanian Shilling
        $currencies['THB'] = array(2, '.', ',');          //  Thailand, Baht
        $currencies['TOP'] = array(2, '.', ',');          //  Tonga, Paanga
        $currencies['AED'] = array(2, '.', ',');          //  UAE Dirham
        $currencies['UAH'] = array(2, ',', ' ');          //  Ukraine, Hryvnia
        $currencies['USD'] = array(2, '.', ',');          //  US Dollar
        $currencies['VUV'] = array(0, '', ',');           //  Vanuatu, Vatu
        $currencies['VEF'] = array(2, ',', '.');          //  Venezuela Bolivares Fuertes
        $currencies['VEB'] = array(2, ',', '.');          //  Venezuela, Bolivar
        $currencies['VND'] = array(0, '', '.');           //  Viet Nam, Dong
        $currencies['ZWD'] = array(2, '.', ' ');          //  Zimbabwe Dollar

        if ($curr == "INR") {
            return $this->formatinr($floatcurr);
        } else {
            return number_format($floatcurr, $currencies[$curr][0], $currencies[$curr][1], $currencies[$curr][2]);
        }
    }

    private function formatinr($input) {
        var_dump('what the hell?');
        //CUSTOM FUNCTION TO GENERATE ##,##,###.##
        $dec = "";
        $pos = strpos($input, ".");
        if ($pos === false) {
            //no decimals   
        } else {
            //decimals
            $dec = substr(round(substr($input, $pos), 2), 1);
            $input = substr($input, 0, $pos);
        }
        $num = substr($input, -3); //get the last 3 digits
        $input = substr($input, 0, -3); //omit the last 3 digits already stored in $num
        while (strlen($input) > 0) { //loop the process - further get digits 2 by 2
            $num = substr($input, -2) . "," . $num;
            $input = substr($input, 0, -2);
        }
        return $num . $dec;
    }

    /**
     * Generate random string
     * 
     * @param type $len: lenght of expected ramdom string
     * @return type
     */
    public static function randomString($len) {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $len; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public static function timeElapsedString($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return Util::translate('time.elapsed.just_now');
        }
        $a = array(12 * 30 * 24 * 60 * 60 => Util::translate('time.elapsed.year'),
            30 * 24 * 60 * 60 => Util::translate('time.elapsed.month'),
            24 * 60 * 60 => Util::translate('time.elapsed.day'),
            60 * 60 => Util::translate('time.elapsed.hour'),
            60 => Util::translate('time.elapsed.minute'),
            1 => Util::translate('time.elapsed.second'));


        while (list($key, $value) = each($a)) {
            $d = $etime / $key;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $value . Util::translate('time.elapsed.ago');            //. ($r > 1 ? 's' : '') 
            }
        }
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

}
