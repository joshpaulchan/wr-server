<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use SimpleValidator\Validator;

class Conversation extends MY_Controller {

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

		// load config
		$this->config->load('wr_config');
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
		return $this->_send_json($data);
	}

	/**
	* Create a reply message and send it to the other participant of the conversation.
	*
	* @post : [success] returns a JSON object with conversation details and messages
	*
	* @param	: int		: id	: the specific page of conversations to reply to
	* @param	: string	: body	: the body of the reply message
	* @return	: array 	: object with conversation and its messages if it exists
	**/
	public function create($id) {
		$json_data = json_decode(file_get_contents('php://input'), TRUE);

		// validate form
		$rules = array(
			// body: 4-256 characters long, and plain text
			"body" => array("required", "max_length(256)", "min_length(4)"),
		);
		$result = Validator::validate($json_data, $rules);

		if ($result->isSuccess() == false) {
			return $this->_send_json(array(
				"error" => true,
				"message" => $result->getErrors()
			));
		}

		$body = $json_data['body'];
		$convo = $this->conversations_model->get_conversation($id);

		// set email config
		$config = [
			'userAgent'	=> $ip = $this->input->ip_address(),
			'mailtype'	=> 'html',
			'crlf'		=> '\r\n',
			'newline'	=> '\r\n'
		];
		// $this->email->initialize($config);

		// configure email
		$our = array(
			'email'	=> $this->config->item('webresponse_email'),
			'name'	=> 'Rutgers UCM Web Staff'
		);

		// TODO: import a template for the body
		$full_body = $body;

		// send email
		$this->email->from($our['email'], $our['name']);
		$this->email->to($convo['emailFrom']);
		$this->email->subject('['.$convo['id'].'] - '.$convo['subject']);
		$this->email->message($full_body);
		// $this->email->send();

		// create record
		return $this->_send_json($this->conversations_model->create_reply(
			$id,
			$convo['emailFrom'],
			$our['email'],
			$full_body
		));
	}

	public function update() {

	}
}

/* End of file conversation.php */
/* Location: ./application/controllers/conversation.php */
