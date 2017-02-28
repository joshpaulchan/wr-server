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
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}

	/**
	* Retrieve a page of users (or queried users).
	*
	* @post : returns a JSON object with links to next queries/pages and pages themselves
	*
	* @param	: int		: page	: the specific page of users to load
	* @param	: int		: n		: the number of items to return per page
	* @param	: string	: q		: the number of items to return per page
	* @return	: array 	: object with user results
	**/
	public function index($page=0, $n=25, $q='') {
		// figure out skips
		$num_convos = $this->users_model->count();
		$total_num_pages = ceil($num_convos / $n);

		// fetch users
		$users = $this->users_model->get_page($page, $n);
		$users = array_map(array($this, '_strip_pw'), $users);

		// create next page link
		$next_str = null;
		if (($page + 1) <= $total_num_pages) {
			$next_str = "/users?page=".($page + 1)."&n=".$n;
		}

		//  create prev page link
		$prev_str = null;
		if ($page > 0) {
			$prev_str = "/users?page=".($page - 1)."&n=".$n;
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
