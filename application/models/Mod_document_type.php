<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document_type extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document_type()
    {
        return $this->db->query("SELECT * FROM document_type")->result_array();
    }

    public function find_document_type_by_id($id)
    {
        return $this->db->query("SELECT * FROM document_type WHERE id = ? OR code = ?", array($id, $id))->result_array();
    }
    public function add_document_type($data)
    {
        return $this->db->insert('document_type', $data);
    }

    public function set_document_type($id, $data)
    {
        return $this->db->where('id', $id)->update('document_type', $data);
    }

    public function remove_document_type($id)
    {
        return $this->db->where('id', $id)->delete('document_type');
    }
}
