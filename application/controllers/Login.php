<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		$this->load->library('session');
		$this->load->library('Encryption');
		$this->load->model('mod_user');
	}

	public function index()
	{
		if (isset($_SESSION['user'])) {
			redirect('dashboard');
		}
		$this->load->view('login');
	}
	public function do_login()
	{
		$user = $this->mod_user->find_user_with_username_and_password($_POST['username'], $_POST['password']);
		if (empty($user)) {
			$_SESION['errmsg'] = 'Perhatian! Username dan password tidak dapat ditemukan.';
			return redirect('login');
		}
		$_SESSION['user'] = $this->mod_user->get_user($user[0]['id'])[0];
		$_SESSION['company_id'] = "1";
		$_SESSION['company_name'] = $this->mod_company->get_company($_SESSION['company_id'])[0]['name'];
		
		
		$data = array('fcm_token' => $_POST['fcm_token']);
		$this->mod_user->set_user($user[0]['id'], $data);
		
		header('location:' . base_url() . 'dashboard');
	}

	public function do_logout()
	{
		$_SESSION['user'] = null;
		redirect('/login');
	}
}
