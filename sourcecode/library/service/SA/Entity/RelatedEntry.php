<?php

/**
 * Object represents table 'do_related_entry'
 *
 * @author: Sililab
 * @date: 2014-05-06 11:39	 
 */
class SA_Entity_RelatedEntry {

    private $id;
    private $entryId;
    private $relatedEntryId;
    private $createTime;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getEntryId() {
        return $this->productId;
    }

    public function setEntryId($entryId) {
        $this->productId = $entryId;
        return $this;
    }

    public function getRelatedEntryId() {
        return $this->relatedEntryId;
    }

    public function setRelatedEntryId($relatedEntryId) {
        $this->relatedEntryId = $relatedEntryId;
        return $this;
    }

    public function getCreateTime() {
        return $this->createTime;
    }

    public function setCreateTime($createTime) {
        $this->createTime = $createTime;
        return $this;
    }

}

?>