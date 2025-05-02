<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document_file extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document_file($document_id)
    {

        return $this->db->query("SELECT * FROM file_document
        WHERE document_id = ?", array($document_id))->result_array();
    }

    public function add_document_file($data)
    {
        $this->db->insert("file_document", $data);
    }

    public function set_document_file($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update("file_document", $data);
    }

    public function remove_document_file($id)
    {
        $this->db->where('document_id', $id);
        $this->db->delete("file_document");
    }
}
