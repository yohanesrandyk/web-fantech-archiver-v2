<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_doctype extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_doctype($id = "%")
    {
        return $this->db->query("SELECT * FROM doctype WHERE id LIKE ? OR code LIKE ?", array($id, $id))->result_array();
    }
    public function add_doctype($data)
    {
        return $this->db->insert('doctype', $data);
    }

    public function set_doctype($id, $data)
    {
        return $this->db->where('id', $id)->update('doctype', $data);
    }

    public function remove_doctype($id)
    {
        return $this->db->where('id', $id)->delete('doctype');
    }
}
