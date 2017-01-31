<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conversation extends CI_Controller {

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
	* Retrieve a conversation and its messages.
	*
	* @post : returns a JSON object with conversation details and messages
	*
	* @param	: int		: id	: the specific page of conversations to load
	* @return	: array 	: object with conversation and its messages if it exists
	**/
	public function index($id) {
		// fetch conversation and its messages
		$data = $this->conversations_model->get_conversation($id);
		$this->_send_json($data);
		// return;
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
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
		// return json_encode($data);
	}
}

/* End of file conversation.php */
/* Location: ./application/controllers/conversation.php */
