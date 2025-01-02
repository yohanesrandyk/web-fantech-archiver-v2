<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document_his extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document_his($id)
    {

        return $this->db->query("SELECT * FROM document_history WHERE document_id = ? ORDER BY update_date DESC", array($id))->result_array();
    }

    public function add_document_his($data)
    {
        $this->db->insert('document_history', $data);
    }
}
