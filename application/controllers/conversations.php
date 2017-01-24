<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conversations extends CI_Controller {

	/**
	* Initialize the database classes and class attributes.
	*
	* @post 	: prevents caching
	*
	**/
	public function __construct() {
		parent::__construct();

		// load db model
		$this->load->model('conversations_model');

		// prevent caching
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}

	/**
	* Retrieve a page of conversations (or queried conversations).
	*
	* @post : returns a JSON object with links to next queries/pages and pages themselves
	*
	* @param	: int		: page	: the specific page of conversations to load
	* @param	: int		: n		: the number of items to return per page
	* @param	: string	: q		: the number of items to return per page
	* @return	: array 	: object with conversation results
	**/
	public function index($page=0, $n=25, $q='') {
		// figure out skips
		$num_convos = $this->conversations_model->count();
		$total_num_pages = ceil($num_convos / $n);

		// fetch conversations
		$conversations = $this->conversations_model->get_page($page, $n);
		// $conversations = [];

		// create next page link
		$next_str = null;
		if (($page + 1) <= $total_num_pages) {
			$next_str = "/conversations?page=".($page + 1)."&n=".$n;
		}

		//  create prev page link
		$prev_str = null;
		if ($page > 0) {
			$prev_str = "/conversations?page=".($page - 1)."&n=".$n;
		}

		$data = [
			"per_page"		=> $n,
			"current_page"	=> $page,
			"next_page_url" => $next_str,
			"prev_page_url" => $prev_str,
			"data" 			=> $conversations
		];

		echo $this->_send_json($data);
		return;
	}

	public function create() {

	}

	public function update() {

	}

	private function _send_json($data) {
		// log vars
		// echo var_dump($data);
		// return;

		// format response
		$this->output->set_content_type('application/json');
		return json_encode($data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
