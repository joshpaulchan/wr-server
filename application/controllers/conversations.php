<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use UAParser\Parser;
use SimpleValidator\Validator;

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

	/**
	* Creates a new conversation w/ initial message.
	*
	* @pre		: inputs must fulfill the validation rules
	* @post 	: [success] returns a JSON object of the created conversation
	* @post 	: [error] returns an error object with an array of validation issues
	*
	* @param	: str		: emailfrom	: the email of the person creating the convo
	* @param	: str		: subject	: the subject of the conversation
	* @param	: str		: body		: the body of the message in the convo
	* @param	: str		: referrer	: the referrer url
	* @return	: array 	: newly created conversation object w/ message or error object
	**/
	public function create() {
		$json_data = json_decode(file_get_contents('php://input'), TRUE);

		// validate form
		$rules = array(
			// email: 4-128 characters long, and a valid email
			"emailfrom" => array("required", "email", "max_length(128)", "min_length(4)"),
			// subject: 4-256 characters long, and plain text
			"subject" => array("required", "alpha_numeric", "max_length(256)", "min_length(4)"),
			// body: 4-256 characters long, and plain text
			"body" => array("required", "max_length(256)", "min_length(4)"),
			// referrer: must be a valid url
			"referrer" => array("required", "url")
		);
		$result = Validator::validate($json_data, $rules);

		if ($result->isSuccess() == false) {
			// return errors
			$resp = array(
				"error" => true,
				"message" => $result->getErrors()
			);
		} else {
			// else create new conversation

			// access variables
			$emailfrom = $json_data['emailfrom'];
			$subject = $json_data['subject'];
			$body = $json_data['body'];
			$referrer = $json_data['referrer'];
			$ip = $this->input->ip_address();
			$useragent = $this->input->user_agent();

			// parse useragent into browser, os
			$parser = Parser::create();
			$parsed_ua =$parser->parse($useragent);
			$data = array(
				"userAgent"	=> $useragent,
				"os"		=> $parsed_ua->os->toString(),
				"browser"	=> $parsed_ua->ua->tostring(),
				"ip"		=> $ip,
				"referrer"	=> $referrer,
			);
			$resp = $this->conversations_model->create($emailfrom, $subject, $body, $data);
		}

		return $this->_send_json($resp);
	}

	public function update() {

	}
}

/* End of file conversations.php */
/* Location: ./application/controllers/conversations.php */
