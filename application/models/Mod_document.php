<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document($id, $type)
    {
        return $this->db->query("SELECT document_2.*, document_all.*, company.code as company_code, company.name AS company_name, from_division.name AS from_division, to_division.name AS to_division, document_type.name AS document_type_name, document_all.id id, document_2.id id_2
        FROM document_all
        INNER JOIN document_$type document_2 ON document_2.document_id = document_all.id
        INNER JOIN division from_division ON from_division.id = document_all.from_division_id
        INNER JOIN division to_division ON to_division.id = document_all.to_division_id
        INNER JOIN company ON company.id = document_all.company_id
        INNER JOIN document_type ON document_type.id = document_all.document_type_id
        WHERE document_all.id LIKE ? AND document_all.status <> 'D'", array($id))->result_array();
    }

    public function find_document_number($document_number)
    {
        return $this->db->query("SELECT id, RIGHT(CONCAT('00000',  COALESCE(COUNT(*), 0) + 1), 5) AS last_document_number FROM document_all WHERE document_number LIKE ?", array($document_number))->result_array();
    }

    public function add_document($type, $data)
    {
        $this->db->insert("document_$type", $data);
        return $this->db->insert_id();
    }

    public function set_document($id, $type, $data)
    {
        $this->db->where('id', $id);
        $this->db->update("document_$type", $data);
    }

    public function remove_document($id, $type)
    {
        $data = array('status' => 'D');
        $this->db->where('id', $id);
        $this->db->update("document_all", $data);
    }
}
