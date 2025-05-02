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
        $data['users'] = $this->mod_user->get_user();
        $this->load->view('layout', $data);
    }

    public function form($id = null)
    {
        $data['user'] = $this->mod_user->get_user($id, '%')[0] ?? [];
        $data['divisions'] = $this->mod_division->get_division();
        $data['content'] = "user/form";
        $data['title'] = "PENGGUNA";
        $this->load->view('layout', $data);
    }

    public function delete($id)
    {
        $data = array(
            'status'          => '0',
        );

        $this->mod_user->set_user($id, $data);

        return redirect('user');
    }

    public function store()
    {
        $_SESSION['old'] = $_POST;

        $id = $this->input->post('id');
        $data = array(
            'username'      => $_POST['username'],
            'password'      => $_POST['password'],
            'fullname'      => $_POST['fullname'],
            'division_id'   => $_POST['division_id'],
            'email'         => $_POST['email'],
            'role'          => $_POST['role'],
            'status'        => 1
        );

        if ($id) {
            $this->mod_user->set_user($id, $data);
        } else {
            $this->mod_user->add_user($data);
        }

        $_SESSION['old'] = null;

        return redirect('user');
    }
}
