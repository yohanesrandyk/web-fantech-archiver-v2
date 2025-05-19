<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_docstatus extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_docstatus($id = "%", $division_id = "%", $doctype_id = '%', $role = '%', $doctype_code = '-')
    {
        $count = $this->db->query("SELECT docstatus.* FROM docstatus WHERE doctype_ids LIKE ?", array($doctype_code))->result_array();

        if (count($count) > 0) {
            $doctype_id = $doctype_code;
        }

        return $this->db->query("SELECT docstatus.*, 
        to_division.name AS to_division 
        FROM docstatus
        LEFT OUTER JOIN division to_division ON to_division.id = docstatus.to_division_id
        WHERE (docstatus.id LIKE ? OR docstatus.code LIKE ?) 
        AND (doctype_ids LIKE 0 OR doctype_ids LIKE ?)
        AND roles LIKE ?
        ORDER BY code_sort", array($id, $id, $doctype_id, $role))->result_array();
    }

    public function add_docstatus($data)
    {
        return $this->db->insert('docstatus', $data);
    }

    public function set_docstatus($id, $data)
    {
        return $this->db->where('id', $id)->update('docstatus', $data);
    }

    public function remove_docstatus($id)
    {
        return $this->db->where('id', $id)->delete('docstatus');
    }
}
