<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document_item extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document_item($document_id, $type)
    {

        return $this->db->query("SELECT * FROM item_document
        WHERE document_id = ?", array($document_id))->result_array();
    }

    public function add_document_item($type, $data)
    {
        $this->db->insert("item_document", $data);
    }

    public function set_document_item($id, $type, $data)
    {
        $this->db->where('id', $id);
        $this->db->update("item_document", $data);
    }

    public function remove_document_item($id, $type)
    {
        $this->db->where('document_id', $id);
        $this->db->delete("item_document");
    }
}
