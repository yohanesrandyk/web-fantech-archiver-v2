<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_dashboard extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_pending_document($division_id = "%", $company_id = "%")
    {
        return $this->db->query("SELECT docstatus.*, document_all.*, docstatus.name as docstatus_name, company.code as company_code, company.name AS company_name, from_division.name AS from_division, to_division.name AS to_division, doctype.name AS doctype_name, document_all.id id
        FROM document_all
        INNER JOIN docstatus ON docstatus.code = document_all.status
        INNER JOIN division from_division ON from_division.id = document_all.from_division_id
        INNER JOIN division to_division ON to_division.id = docstatus.to_division_id
        INNER JOIN company ON company.id = document_all.company_id
        INNER JOIN doctype ON doctype.id = document_all.doctype_id
        WHERE document_all.status <> 'D'
        AND docstatus.to_division_id LIKE ?
        AND document_all.company_id LIKE ?", array($division_id, $company_id))->result_array();
    }

    public function get_submit_document($division_id = "%", $company_id = "%")
    {
        return $this->db->query("SELECT docstatus.*, document_all.*, docstatus.name as docstatus_name, company.code as company_code, company.name AS company_name, from_division.name AS from_division, to_division.name AS to_division, doctype.name AS doctype_name, document_all.id id
        FROM document_all
        INNER JOIN docstatus ON docstatus.code = document_all.status
        INNER JOIN division from_division ON from_division.id = document_all.from_division_id
        INNER JOIN division to_division ON to_division.id = docstatus.to_division_id
        INNER JOIN company ON company.id = document_all.company_id
        INNER JOIN doctype ON doctype.id = document_all.doctype_id
        WHERE document_all.status <> 'D'
        AND document_all.from_division_id LIKE ?
        AND document_all.company_id LIKE ?", array($division_id, $company_id))->result_array();
    }
    public function get_sum_document($type, $division_id = "%")
    {
        return $this->db->query("SELECT SUM(unit*price) total, doctype.name 
        FROM item_document_$type 
        INNER JOIN document_all ON document_all.id = item_document_$type.document_id
        INNER JOIN doctype ON doctype.id = document_all.doctype_id 
        WHERE document_all.from_division_id LIKE ?
        AND document_all.company_id = ?
        GROUP BY doctype.name", array($division_id, $_SESSION['company_id']))->result_array();
    }
}
