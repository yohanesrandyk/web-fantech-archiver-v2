<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document_his extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document_his($id = '%', $status = '%')
    {

        return $this->db->query("SELECT * FROM document_history
        INNER JOIN docstatus ON document_history.status_update = docstatus.code
        WHERE document_id LIKE ?
        AND status_update LIKE ? ORDER BY update_date DESC", array($id, $status))->result_array();
    }

    public function add_document_his($data)
    {
        $this->db->insert('document_history', $data);
    }
}
