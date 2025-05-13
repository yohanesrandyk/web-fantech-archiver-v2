<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_transfer_document extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_transfer_document($document_id)
    {

        return $this->db->query("SELECT * FROM transfer_document
        WHERE document_id = ?", array($document_id))->result_array();
    }

    public function add_transfer_document($data)
    {
        $this->db->insert("transfer_document", $data);
    }

    public function set_transfer_document($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update("transfer_document", $data);
    }

    public function remove_transfer_document($id)
    {
        $this->db->where('document_id', $id);
        $this->db->delete("transfer_document");
    }
}
