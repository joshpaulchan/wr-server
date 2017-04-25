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

		// mark it as read
		$this->conversations_model->set_unread($id, false);
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
			$this->conversations_model->set_unreplied($convo['id'], false);
		} catch (Exception $e) {
			$resp = array(
				"error" => true,
				"message" => "There was an error sending your message."
			);
		}
		return $this->_send_json($resp);
	}

	/**
	* Forward a conversation and send the thread to the specified recipient.
	*
	* @post : [success] returns a JSON object with conversation details and messages
	*
	* @param	: int		: id		: the specific page of conversations to reply to
	* @param	: string	: forwardTo	: the email to forward the conversation to
	* @param	: string	: body		: the body of the reply message
	* @return	: array 	: object with conversation and its messages if it exists
	**/
	public function forward($id) {
		$json_data = $this->get_json();

		// validate form
		// forwardTo: 4-128 character long email
		$this->form_validation->set_rules('forwardTo', 'forwardTo', 'trim|required|min_length[4]|max_length[128]|valid_email');
		// body: 4-256 characters long, and plain text
		$this->form_validation->set_rules('body', 'body', 'trim|min_length[4]|max_length[256]');
		$result = $this->form_validation->run();

		if ($result == false) {
			return $this->_send_json(array(
				"error" => true,
				"message" => validation_errors()
			));
		}

		$forwardTo = $json_data['forwardTo'];
		$body = (array_key_exists('body', $json_data) ? $json_data['body'] : '');
		$convo = $this->conversations_model->get_conversation($id);

		$our = array(
			'email'	=> $this->config->item('webresponse_email'),
			'name'	=> $this->config->item('webresponse_name')
		);

		// concatenate thread to string and forward to
		$full_body = $body.$this->_stringify_thread($convo['messages']);
		// echo $full_body;
		// return;

		// send email
		$this->email->from($our['email'], $our['name']);
		$this->email->to($forwardTo);
		$this->email->subject('fwd: ['.$convo['id'].'] - '.$convo['subject']);
		$this->email->message($full_body);


		try {
			$this->email->send();
			// FIXME: create own forward record in a forward table?
			$full_body = "<small>forwarded to: <strong>".$forwardTo."</strong></small><br/><br/>".$full_body;
			$resp = $this->conversations_model->create_reply(
				$id,
				$convo['emailFrom'],
				$our['email'],
				$full_body
			);
			$resp = array('error' => false);
		} catch (Exception $e) {
			$resp = array(
				"error" => true,
				"message" => "There was an error forwarding this message."
			);
		}
		return $this->_send_json($resp);
	}

	// Turn list of message objects into a giant string
	//
	private function _stringify_thread($messages) {
		$s = '';

		$s .= "<br/><br/><span>Begin forwarded message:</span>"; //

		foreach ($messages as $msg) {
			$s .= "<br/><br/><span>==========</span> "; // top delimiter (2 newlines and 10 ='s')
			$s .= "<span>&lang;".$msg['emailFrom']."&rang;'s message at ".$msg['createdAt']."</span><br/><br/>";
			$s .= $msg['body'];
		}

		return $s;
	}

	public function update() {

	}
}

/* End of file conversation.php */
/* Location: ./application/controllers/conversation.php */
