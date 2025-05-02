<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class Division extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_division');
    }

    public function index()
    {
        $data['content'] = "division/index";
        $data['title'] = "DIVISI";
        $data['divisions'] = $this->mod_division->get_division();
        $this->load->view('layout', $data);
    }

    public function form($id = null)
    {
        $data['division'] = $this->mod_division->get_division($id)[0] ?? [];
        $data['content'] = "division/form";
        $data['title'] = "DIVISI";
        $this->load->view('layout', $data);
    }

    public function store()
    {
        $_SESSION['old'] = $_POST;

        $id = $this->input->post('id');
        $data = array(
            'code' => strtoupper($_POST['code']),
            'name' => $_POST['name']
        );

        if ($id) {
            $this->mod_division->set_division($id, $data);
        } else {
            $this->mod_division->add_division($data);
        }

        $_SESSION['old'] = null;

        return redirect('division');
    }

    public function delete($id)
    {
        $this->mod_division->remove_division($id);
        return redirect('division');
    }
}
