<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class Doctype extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_document_type');
        $this->load->model('mod_division');
    }

    public function index()
    {
        $data['content'] = "document_type/index";
        $data['title'] = "TIPE DOKUMEN";
        $this->load->view('layout', $data);
    }

    public function form()
    {
        $data['content'] = "document_type/form";
        $data['title'] = "TIPE DOKUMEN";
        $this->load->view('layout', $data);
    }

    public function store()
    {
        $id = $this->input->post('id');
        $data = array(
            'code'          => strtoupper($_POST['code']),
            'name'          => $_POST['name'],
        );

        if ($id) {
            $this->mod_document_type->set_document_type($id, $data);
        } else {
            $this->mod_document_type->add_document_type($data);
        }

        return redirect('doctype');
    }

    public function delete($id)
    {
        $this->mod_document_type->remove_document_type($id);
        return redirect('doctype');
    }
}
