<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_document extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_document($id, $type, $division_id = "%", $company_id = "%")
    {
        return $this->db->query(
            "SELECT docstatus.*, document_2.*, document_all.*, docstatus.name as docstatus_name, company.code as company_code, company.name AS company_name, from_division.name AS from_division, to_division.name AS to_division, doctype.name AS doctype_name, doctype.code AS doctype_code, doctype.type AS doctype_type, document_all.id id, document_2.id id_2, 
        CASE 
            WHEN document_all.from_division_id LIKE ?
            OR docstatus.to_division_id LIKE ? 
            THEN 1 ELSE 0 END AS is_division,
        IFNULL((SELECT SUM(unit * price) FROM item_document WHERE document_id = document_all.id), 0) AS transfer_amount_2
        FROM document_all
        INNER JOIN docstatus ON docstatus.code = document_all.status
        INNER JOIN document_$type document_2 ON document_2.document_id = document_all.id
        INNER JOIN division from_division ON from_division.id = document_all.from_division_id
        INNER JOIN division to_division ON to_division.id = docstatus.to_division_id
        INNER JOIN company ON company.id = document_all.company_id
        INNER JOIN doctype ON doctype.id = document_all.doctype_id
        WHERE document_all.id LIKE ? AND document_all.status <> 'D'
        AND (document_all.from_division_id LIKE ?
            OR docstatus.to_division_id LIKE ? AND docstatus.roles LIKE ?
            OR document_all.id IN (SELECT document_id FROM document_history WHERE from_division_id LIKE ?))
        AND document_all.company_id LIKE ?
        AND (('$type' = 'all' AND doctype.type = 'A') OR ('$type' <> 'all' AND doctype.type <> 'A'))
        AND (
            (LEFT(document_all.status, 1) = 'N' AND document_all.user_create_id = ?) 
            OR (LEFT(document_all.status, 1) = 'N' AND document_all.id IN (SELECT document_id FROM document_history WHERE user_update_id = ?)) 
            OR LEFT(document_all.status, 1) <> 'N'
        )
        ORDER BY document_all.id DESC",
            array(
                $division_id,
                $division_id,
                $id,
                $division_id,
                $division_id,
                '%' . $_SESSION['user']['role'] . '%',
                $division_id,
                $company_id,
                $_SESSION['user']['id'],
                $_SESSION['user']['id'],
            )
        )->result_array();
    }

    public function get_document_number($document_number)
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
