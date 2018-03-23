<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailThread extends Thread {

    private $to;
    private $subject;
    private $body;
    private $bcc;
    private $receiverName;

    public function __construct($to, $receiverName, $subject, $body, $bcc = false) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->bcc = $bcc;
        $this->receiverName = $receiverName;
    }

    public
            function run() {

        $retval = TRUE;
        try {
            $param = Services::createConfigurationService()->get("system.email");
            if ($param != NULL) {
                $emailConfig = json_decode($param->getValue(), true);
                $retval = TRUE;

//                $config = array('host' => 'mail.vnbuyers.com',
                $config = array('host' => 'smtp.gmail.com',
                    'port' => 587,
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $emailConfig["email"],
                    'password' => $emailConfig["password"]);
//                $transport = new Zend_Mail_Transport_Smtp('mail.vnbuyers.com', $config);
                $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
                $mail = new Zend_Mail('UTF-8');
                $mail->setBodyHtml($this->body);
                $mail->setFrom('contact@vnbuyers.com', Util::translate("deal.email.from.name"));
                $mail->addTo($this->to, $this->receiverName);
                if ($this->bcc)
                    $mail->addBcc($emailConfig["email"]);
                $mail->setSubject($this->subject);
                $mail->send($transport);
            } else {
                $retval = FALSE;
            }
        } catch (Exception $exc) {
            $retval = FALSE;
        }
        return $retval;
    }

}

?>
