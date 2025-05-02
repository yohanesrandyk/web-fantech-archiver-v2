<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class Doctype extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_doctype');
        $this->load->model('mod_division');
    }

    public function index()
    {
        $data['content'] = "doctype/index";
        $data['title'] = "TIPE DOKUMEN";
        $data['doctypes'] = $this->mod_doctype->get_doctype();
        $this->load->view('layout', $data);
    }

    public function form($id = null)
    {
        $data['doctype'] = $this->mod_doctype->get_doctype($id)[0] ?? [];
        $data['content'] = "doctype/form";
        $data['title'] = "TIPE DOKUMEN";
        $this->load->view('layout', $data);
    }

    public function store()
    {
        $_SESSION['old'] = $_POST;

        $id = $this->input->post('id');
        $data = array(
            'code'          => strtoupper($_POST['code']),
            'name'          => $_POST['name'],
        );

        if ($id) {
            $this->mod_doctype->set_doctype($id, $data);
        } else {
            $this->mod_doctype->add_doctype($data);
        }

        $_SESSION['old'] = null;

        return redirect('doctype');
    }

    public function delete($id)
    {
        $this->mod_doctype->remove_doctype($id);
        return redirect('doctype');
    }
}
