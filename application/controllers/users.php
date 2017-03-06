<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	/**
	* Initialize the database classes and class attributes.
	*
	* @post 	: prevents caching
	*
	**/
	public function __construct() {
		parent::__construct();

		// load db model
		$this->load->model('users_model');

		// prevent caching
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}

	/**
	* Retrieve a page of users (approved/unapproved).
	*
	* @post : returns a JSON object with links to next queries/pages and pages themselves
	*
	* @param	: bool		: approval	: the approval status to filter for
	* @param	: int		: page		: the specific page of users to load
	* @param	: int		: n			: the number of items to return per page
	* @return	: array 	: array with user objects
	**/
	public function index() {
		// access vars
		$approv_str = $this->input->get('approval', '');
		$approval = ($approv_str === 'true');
		$page = ((int)$this->input->get('page') >= 0) ? (int)($this->input->get('page')) : 0;
		$n = ((int)$this->input->get('n') > 0) ? (int)($this->input->get('n')) : 25;

		// figure out skips
		$num_convos = $this->users_model->count($approval);
		$total_num_pages = ceil($num_convos / $n);

		// fetch users
		$users = $this->users_model->get_page_with_approval($approval, $page, $n);
		$users = array_map(array($this, '_strip_pw'), $users);

		// create next page link
		$next_str = null;
		if (($page + 1) <= $total_num_pages) {
			$next_str = "/users?page=".($page + 1)."&n=".$n."&approval=".$approv_str;
		}

		//  create prev page link
		$prev_str = null;
		if ($page > 0) {
			$prev_str = "/users?page=".($page - 1)."&n=".$n."&approval=".$approv_str;
		}

		$data = array(
			"per_page"		=> $n,
			"current_page"	=> $page,
			"next_page_url" => $next_str,
			"prev_page_url" => $prev_str,
			"data" 			=> $users
		);

		$this->_send_json($data);
	}

	/**
	* Strips the password from the given user object.
	*
	* @post 	: the user object will be copied, and the 'password' key removed.
	*
	* @param	: array 	: user	: the user object to strip the pw from
	* @return	: array 	: the copied user object sans pw
	**/
	private function _strip_pw($user) {
		return array_diff_key($user, array("password" => ''));
	}

	private function _send_json($data) {
		// log vars
		// echo var_dump($data);
		// return;

		// format response
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
