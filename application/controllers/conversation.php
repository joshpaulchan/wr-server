<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// use SimpleValidator\Validator;

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

		// load form validation lib
		$this->load->library('form_validation');

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
	public function reply($id) {
		$json_data = $this->get_json();

		// validate form
		// body: 4-256 characters long, and plain text
		$this->form_validation->set_rules('body', 'body', 'trim|required|min_length[4]|max_length[256]');
		$result = $this->form_validation->run();

		if ($result == false) {
			return $this->_send_json(array(
				"error" => true,
				"message" => validation_errors()
			));
		}

		$body = $json_data['body'];
		$convo = $this->conversations_model->get_conversation($id);

		$our = array(
			'email'	=> $this->config->item('webresponse_email'),
			'name'	=> $this->config->item('webresponse_name')
		);

		// TODO: import a template for the body
		$full_body = $body;

		// send email
		$this->email->from($our['email'], $our['name']);
		$this->email->to($convo['emailFrom']);
		$this->email->subject('['.$convo['id'].'] - '.$convo['subject']);
		$this->email->message($full_body);

		try {
			$this->email->send();
			// create record
			$resp = $this->conversations_model->create_reply(
				$id,
				$convo['emailFrom'],
				$our['email'],
				$full_body
			);
		} catch (Exception $e) {
			$resp = array(
				"error" => true,
				"message" => "There was an error sending your message."
			);
		}
		return $this->_send_json($resp);
	}

	public function update() {

	}
}

/* End of file conversation.php */
/* Location: ./application/controllers/conversation.php */
