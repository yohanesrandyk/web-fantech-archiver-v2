<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_company extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_company($id = "%")
    {
        return $this->db->query("SELECT * FROM company WHERE id LIKE ?", array($id))->result_array();
    }

    public function add_company($data)
    {
        return $this->db->insert('company', $data);
    }

    public function set_company($id, $data)
    {
        return $this->db->where('id', $id)->update('company', $data);
    }

    public function remove_company($id)
    {
        return $this->db->where('id', $id)->delete('company');
    }
}
