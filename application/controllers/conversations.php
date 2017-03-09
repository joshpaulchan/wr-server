<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conversations extends MY_Controller {

	/**
	* Initialize the database classes and class attributes.
	*
	**/
	public function __construct() {
		parent::__construct();

		// load db model
		$this->load->model('conversations_model');
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
	public function index() {
		// access vars
		$page = ($this->input->get('page') != NULL) ? (int)$this->input->get('page') : 0;
		$n = ($this->input->get('n') != NULL) ? (int)$this->input->get('n') : 25;
		$q = ($this->input->get('q') != NULL) ? (int)$this->input->get('q') : null;

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

		$this->_send_json($data);
	}

	public function create() {

		// validate form

		// return errors

		// else create new conversation

	}

	public function update() {

	}
}

/* End of file conversations.php */
/* Location: ./application/controllers/conversations.php */
