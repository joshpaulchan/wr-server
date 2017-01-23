<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conversations extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
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
	public function list($page=0, $n=0, $q='') {
		$conversations = [];
		return [
			"per_page"		=> $n,
			"current_page"	=> $page,
			"next_page_url" => "/conversations?page=".($n + 1)."&n=".$n,
			"prev_page_url" => "/conversations?page=".($n - 1)."&n=".$n,
			"data" 			=> $conversations
		];

	}

	public function create() {

	}

	public function update() {

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
