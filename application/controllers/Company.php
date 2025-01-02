<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class Company extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_company');
    }

    public function index()
    {
        $data['content'] = "company/index";
        $data['title'] = "PERUSAHAAN";
        $this->load->view('layout', $data);
    }

    public function form()
    {
        $data['content'] = "company/form";
        $data['title'] = "PERUSAHAAN";
        $this->load->view('layout', $data);
    }

    public function store()
    {
        $id = $this->input->post('id');
        $data = array(
            'code' => strtoupper($_POST['code']),
            'name' => $_POST['name'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone']
        );

        if ($id) {
            $this->mod_company->set_company($id, $data);
        } else {
            $this->mod_company->add_company($data);
        }

        return redirect('company');
    }

    public function delete($id)
    {
        $this->mod_company->remove_company($id);
        return redirect('company');
    }
}
