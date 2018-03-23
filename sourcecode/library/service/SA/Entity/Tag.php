<?php

/**
 * Object represents table 'sa_card'
 *
 * @author: Sililab
 * @date: 2014-06-23 04:47	 
 */
class SA_Entity_Tag {

    private $id;
    private $type;
    private $name;

    public function getId() {
        return $this->id;
    }

    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType( $type ) {
        $this->type = $type;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName( $name ) {
        $this->name = $name;
        return $this;
    }

}

?>