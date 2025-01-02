<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('mod_user');
    }

    public function index()
    {
        $data['content'] = "dashboard";
        $data['title'] = "DASHBOARD";
        $this->load->view('layout', $data);
    }
}
