<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_user');
        $this->load->model('mod_division');
    }

    public function index()
    {
        $data['content'] = "user/index";
        $data['title'] = "PENGGUNA";
        $this->load->view('layout', $data);
    }

    public function form()
    {
        $data['content'] = "user/form";
        $data['title'] = "PENGGUNA";
        $this->load->view('layout', $data);
    }

    public function store()
    {
        $id = $this->input->post('id');
        $data = array(
            'username'      => $_POST['username'],
            'password'      => $_POST['password'],
            'fullname'      => $_POST['fullname'],
            'division_id'   => $_POST['division_id'],
            'email'         => $_POST['email'],
            'role'          => $_POST['role'],
        );

        if ($id) {
            $this->mod_user->set_user($id, $data);
        } else {
            $this->mod_user->add_user($data);
        }

        return redirect('user');
    }

    public function delete($id)
    {
        $this->mod_user->remove_user($id);
        return redirect('user');
    }
}
