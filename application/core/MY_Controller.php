<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	/**
	* Initialize the database classes and class attributes.
	*
	* @post 	: prevents caching
	*
	**/
	public function __construct() {
		parent::__construct();

		// initialize sessions
		$this->load->library('session');

		// prevent caching
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}

	public function _send_json($data, $code=200) {
		// log vars
		// echo var_dump($data);
		// return;

		// format response
		$this->output
			->set_status_header($code)
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	/**
	* Checks the session data to see if user is logged in.
	*
	* @return	: bool	: true if user is logged in, false o.w.
	**/
	public function _is_logged_in() {
		$sess_data = $this->session->all_userdata();
		return array_key_exists("user", $sess_data);
	}

	/**
	* Checks the session data to see if user is logged in and is an admin.
	*
	* @return	: bool	: true if user is logged in and admin, false o.w.
	**/
	public function _is_admin() {
		$sess_data = $this->session->all_userdata();
		return array_key_exists("user", $sess_data) && $sess_data['user']['admin'] === true;
	}

	/**
	* Checks the session data to see if user is logged in and is an admin.
	*
	* @return	: array	: user object if logged in, empty object otherwise
	**/
	public function get_user() {
		if ($this->_is_logged_in()) {
			$sess_data = $this->session->all_userdata();
			return $sess_data['user'];
		}
		return array();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
