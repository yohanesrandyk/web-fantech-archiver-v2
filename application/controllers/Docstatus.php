<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class docstatus extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_docstatus');
        $this->load->model('mod_doctype');
        $this->load->model('mod_division');
    }

    public function index()
    {
        $data['content'] = "docstatus/index";
        $data['title'] = "STATUS DOKUMEN";
        $data['doctypes'] = $this->mod_doctype->get_doctype();
        $this->load->view('layout', $data);
    }

    public function form()
    {
        $data['docstatus'] = $this->mod_docstatus->get_docstatus("%", "%", (isset($_GET['type']) ? '%' . $_GET['type'] . '%' : null), "%") ?? [];
        $data['divisions'] = $this->mod_division->get_division();
        $data['doctypes'] = $this->mod_doctype->get_doctype();
        $data['content'] = "docstatus/form";
        $data['title'] = "STATUS DOKUMEN";
        $this->load->view('layout', $data);
    }

    public function store()
    {
        $_SESSION['old'] = $_POST;

        for ($i = 0; $i < count($_POST['id']); $i++) {
            $data = array(
                'code'              => strtoupper($_POST['code'][$i]),
                'name'              => $_POST['name'][$i],
                "to_division_id"    => $_POST["to_division_id"][$i],
                "cc_division_ids"   => $_POST["cc_division_ids"][$i],
                'approve_code'      => strtoupper($_POST['approve_code'][$i]),
                'reject_code'       => strtoupper($_POST['reject_code'][$i]),
                'code_sort'         => $i,
            );

            if (!empty($_POST['id'][$i])) {
                $this->mod_docstatus->set_docstatus($_POST['id'][$i], $data);
            } else if (!empty($_POST['code'][$i])) {
                $this->mod_docstatus->add_docstatus($data);
            }
        }

        $_SESSION['old'] = null;

        return redirect('docstatus');
    }

    public function delete($id)
    {
        $this->mod_docstatus->remove_docstatus($id);
        return redirect('docstatus');
    }
}
