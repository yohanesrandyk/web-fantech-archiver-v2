<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_notification extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_notification($division_id)
    {

        return $this->db->query("SELECT * FROM notification
        WHERE to_division_id = ?", array($division_id))->result_array();
    }

    public function add_notification($data)
    {
        $this->db->insert("notification", $data);
    }

    public function set_notification($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update("notification", $data);
    }

    public function remove_notification($id)
    {
        $this->db->where('document_id', $id);
        $this->db->delete("notification");
    }
}
