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

		// load email lib
		$this->load->library('email');

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

	/**
	* TODO: Create a reply message and send it to the other participant of the conversation.
	*
	* @post : returns a JSON object with conversation details and messages
	*
	* @param	: int		: id	: the specific page of conversations to reply to
	* @param	: string	: body	: the body of the reply message
	* @return	: array 	: object with conversation and its messages if it exists
	**/
	public function create($id, $body) {
		$convo = $this->conversations_model->get_conversation($id);

		// set email config
		$config = [
			'userAgent'	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
			'mailtype'	=> 'html',
			'crlf'		=> '\r\n',
			'newline'	=> '\r\n'
		];
		$this->email->initialize($email_config);

		// configure email
		$our = [
			'email'	=> 'webstaff@ucm.rutgers.edu',
			'name'	=> 'Rutgers UCM Web Staff'
		];
		// FIXME: import a template for the body
		$full_body = $body;

		// send email
		$this->email->from($our->email, $our->name);
		$this->email->to($convo->emailFrom);
		$this->email->subject('['.$convo->id.'] - '.$convo->subject);
		$this->email->message($full_body);
		$this->email->send();

		// create record
		$this->conversations_model->create_reply(
			$id,
			$our->email,
			$convo->emailFrom,
			$full_body
		);
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
