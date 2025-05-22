<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_user');
        $this->load->model('mod_dashboard');
        $this->load->model('mod_docstatus');
    }

    public function index()
    {
        $data['content'] = "dashboard";
        $data['title'] = "DASHBOARD";

        if (isset($_SESSION['user'])) {
            $data["document_pending"] = [];
            $data["document_submit"] = [];

            $documents = [];
            $documents = array_merge($documents, $this->mod_document->get_document("%", 'all', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%"));
            $documents = array_merge($documents, $this->mod_document->get_document("%", 'ca', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%"));
            $documents = array_merge($documents, $this->mod_document->get_document("%", 'pc', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%"));
            $documents = array_merge($documents, $this->mod_document->get_document("%", 'pp', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%"));
            $documents = array_merge($documents, $this->mod_document->get_document("%", 'pr', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%"));
            foreach ($documents as $document) {
                $doctype_code = strtolower($document['doctype_code']);
                if ($doctype_code != 'ca' && $doctype_code != 'pc' && $doctype_code != 'pp' && $doctype_code != 'pr') {
                    $doctype_code = 'all';
                }

                $docstatus = $this->mod_docstatus->get_docstatus('%', $_SESSION['user']['division_id'], '%' . $doctype_code . '%', '%' . $_SESSION['user']['role'] . '%');

                $approvecode = $document['approve_code'];
                $iscanapprove = substr($approvecode, 0, 1) == 'A';
                $iscanapprove = $iscanapprove && ($approvecode != 'A' || ($approvecode == 'A' && ($document['user_create_id']) != $_SESSION['user']['id']));
                $isinoptions = false;
                foreach ($docstatus as $docstatus_) {
                    if (($document['from_division_id'] ?? '') == $_SESSION['user']['division_id']) {
                        if (
                            substr($docstatus_['code'], 0, 1) != 'N'
                            && substr($docstatus_['code'], 0, 1) != 'P'
                            && substr($docstatus_['code'], 0, 1) != 'R'
                            && $docstatus_['approve_code'] != ''
                            && !str_contains($docstatus_['name'], '_HEAD')
                        ) {
                            continue;
                        }
                    }
                    $options[] = array($docstatus_['code'], $docstatus_['name'] . ' [' . $docstatus_['code'] . ']');
                    if ($docstatus_['code'] == $approvecode) {
                        $isinoptions = true;
                    }
                }
                $iscanapprove = $iscanapprove && $isinoptions;


                $isreview = (
                    $document["from_division_id"] != $_SESSION['user']['division_id']
                    && $document["to_division_id"] != $_SESSION['user']['division_id']
                ) ?? false;

                if ($iscanapprove && !$isreview)
                    array_push($data['document_pending'], $document);

                if ($document["from_division_id"] == $_SESSION['user']['division_id'])
                    array_push($data['document_submit'], $document);
            }

            $data["document_sum_ca"] = $this->mod_dashboard->get_sum_document('ca', $_SESSION['user']['division_id'])[0] ?? array('total' => '0');
            $data["document_sum_pc"] = $this->mod_dashboard->get_sum_document('pc', $_SESSION['user']['division_id'])[0] ?? array('total' => '0');
            $data["document_sum_pp"] = $this->mod_dashboard->get_sum_document('pp', $_SESSION['user']['division_id'])[0] ?? array('total' => '0');
        }

        $this->load->view('layout', $data);
    }

    public function crosspt($id)
    {
        $_SESSION['company_id'] = $id;
        $_SESSION['company_name'] = $this->mod_company->get_company($id)[0]['name'];
        return redirect($_SERVER['HTTP_REFERER']);
    }
}
