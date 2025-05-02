<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_division extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_division($id = "%")
    {
        return $this->db->query("SELECT * FROM division WHERE id LIKE ?", array($id))->result_array();
    }
    public function add_division($data)
    {
        return $this->db->insert('division', $data);
    }

    public function set_division($id, $data)
    {
        return $this->db->where('id', $id)->update('division', $data);
    }

    public function remove_division($id)
    {
        return $this->db->where('id', $id)->delete('division');
    }
}
