<?php

/**
 * Object represents table 'sa_notification'
 *
 * @author: ThoMC
 * @date: 2014-09-12 04:47	 
 */
class SA_Entity_Notification {

    const TYPE_NEW_ORDER = "order";
    const TYPE_CUSTOMER_REGISTER = "customer_register";
    const TYPE_SUPPLIER_REGISTER = "supplier_register";
    const TYPE_FEEDBACK = "feedback";

    private $id;
    private $type;
    private $new;
    private $content;
    private $time;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getNew() {
        return $this->new;
    }

    public function setNew($new) {
        $this->new = $new;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * 
     * @return DateTime
     */
    public function getTime() {
        return $this->time;
    }

    public function setTime($createTime) {
        $this->time = $createTime;
        return $this;
    }

}

?>