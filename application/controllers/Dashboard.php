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
    }

    public function index()
    {
        $data['content'] = "dashboard";
        $data['title'] = "DASHBOARD";
        
        if(isset($_SESSION['user'])){
            $data["document_pending"] = $this->mod_dashboard->get_pending_document($_SESSION['user']['division_id'], $_SESSION['company_id']);
            $data["document_submit"] = $this->mod_dashboard->get_submit_document($_SESSION['user']['division_id'], $_SESSION['company_id']);
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
