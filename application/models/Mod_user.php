<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_user extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_user($division_id = '%')
    {
        return $this->db->query("SELECT user.*, division.name AS division_name FROM user INNER JOIN division ON division.id = user.division_id WHERE user.division_id LIKE ?", array($division_id))->result_array();
    }

    public function find_user_with_username_and_password($username, $password)
    {
        return $this->db->query("SELECT * FROM user WHERE username = ? AND password = ?", array($username, $password))->result_array();
    }

    public function find_user_by_id($id)
    {
        return $this->db->query("SELECT * FROM user WHERE id = ?", array($id))->result_array();
    }

    public function add_user($data)
    {
        return $this->db->insert('user', $data);
    }

    public function set_user($id, $data)
    {
        return $this->db->where('id', $id)->update('user', $data);
    }

    public function remove_user($id)
    {
        return $this->db->where('id', $id)->delete('user');
    }
}
