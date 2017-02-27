<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Hautelook\Phpass\PasswordHash;
require_once __DIR__.'/../third_party/autoload.php';

class Auth extends CI_Controller {

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

		// initialize password hasher
		$this->pwHasher = new PasswordHash(8, false);

		// initialize sessions
		$this->load->library('session');
	}

	/**
	* Attempt to log into web response.
	*
	* @post : returns a JSON object with user info, minus pw
	*
	* @param	: string	: email		: the email of the user attempting login
	* @param	: string	: password	: the password of the user attempting login
	* @return	: array 	: object with conversation results
	**/
	public function login() {
		// Access variables
		$email = $this->input->post('email');
		$pw = $this->input->post('password');

		// Get user by email
		$data = $this->users_model->get_user_by_email($email);

		if (array_key_exists('error', $data)) {
			return $this->_send_json([
				'error' => true,
				'message' => "Error logging user in."
			], 500);
		}

		// Check if approved
		if ($data['approved'] === false) {
			return $this->_send_json([
				'error' => true,
				'message' => 'You have not be approved to use Web-Response yet.'
			], 401);
		}

		// Check password
		if (!$this->_passwords_match($pw, $data['password'])) {
			return $this->_send_json([
				'error' => true,
				'message' => 'Password is incorrect.'
			], 401);
		}
		// remove pw
		unset($data["password"]);

		// add user to session
		$this->session->set_userdata(["user" => $data]);

		$this->_send_json($data);
	}

	/**
	* Attempt to log user out of web response.
	*
	* @post : returns an empty JSON object
	*
	* @param	: string	: email		: the email of the user attempting login
	* @param	: string	: password	: the password of the user attempting login
	* @return	: null
	**/
	public function logout() {
		// delete session
		if ($this->_is_logged_in()) { $this->session->sess_destroy(); }

		return null;
	}

	/**
	* Checks if user is currently logged in.
	*
	* @post	: returns a JSON object with a boolean "loggedIn" key, depending on
	* if a user is logged in or not.
	*
	* @return	: array 	: JSON object with 'loggedIn' key being true is user in
	* session, false otherwise.
	**/
	public function loggedIn() {
		$this->_send_json([ "loggedIn" => $this->_is_logged_in() ]);
	}

	/**
	* Submits an account creation request.
	*
	* @param	: string	: email		: the email of the user attempting login
	* @param	: string	: password	: the password of the user attempting login
	* @return	: null if successful, error object o.w.
	**/
	public function register() {
		// validate form
		$email = $this->input->post('email');
		$pw = $this->input->post('password');

		// check values exists
		if (!$email || !$pw) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'Error registering the user.'
			]);
		}

		// check email hasn't been used
		$resp = $this->users_model->get_user_by_email($email);

		if (!array_key_exists('error', $resp)) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'Error registering the user.'
			], 500);
		}

		// create new user
		$u = $this->users_model->create($email, $this->pwHasher->HashPassword($pw));

		return null;

	}

	/**
	* Escalates a regular user to admin status.
	*
	* @pre		: the user submitting this request must be an admin user
	*
	* @return	: null if successful, error object o.w.
	**/
	public function escalate($id) {
		// check logged in
		if (!$this->_is_logged_in()) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'You are not logged in.'
			], 401);
		}

		$user = $this->session->all_userdata()['user'];

		// verify admin status
		if ($user['admin'] === false) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'You do not have the permissions to escalate users.'
			], 403);
		}

		// escalate user
		$u = $this->users_model->set_admin($id, true);

		return null;
	}

	/**
	* Deescalates an admin user to a regular user.
	*
	* @pre		: the user submitting this request must be an admin user
	*
	* @return	: null if successful, error object o.w.
	**/
	public function deescalate($id) {
		// check logged in
		if (!$this->_is_logged_in()) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'You are not logged in.'
			], 401);
		}

		$user = $this->session->all_userdata()['user'];

		// verify admin status
		if ($user['admin'] === false) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'You do not have the permissions to deescalate users.'
			], 403);
		}

		// escalate user
		$u = $this->users_model->set_admin($id, false);

		return null;
	}

	/**
	* Deescalates an admin user to a regular user.
	*
	* @pre		: the user submitting this request must be an admin user
	*
	* @return	: null if successful, error object o.w.
	**/
	public function approve($id) {
		// check logged in
		if (!$this->_is_logged_in()) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'You are not logged in.'
			], 401);
		}

		$user = $this->session->all_userdata()['user'];

		// verify admin status
		if ($user['admin'] === false) {
			return $this->_send_json([
				'error' => true,
				'errorMessage' => 'You do not have the permissions to approve users.'
			], 403);
		}

		// escalate user
		$u = $this->users_model->approve($id);

		return null;
	}

	/**
	* Check each password to see if they are equivalent.
	*
	* @param	: String	: pwa	: the first password to compare
	* @param	: String	: pwb	: the second password to compare
	* @return	: Boolean	: true if the passwords match, false o.w.
	**/
	private function _passwords_match($pwa, $pwb) {
		return $this->pwHasher->CheckPassword($pwa, $pwb);
	}

	/**
	* Checks the session data to see if user is logged in.
	*
	* @return	: bool	: true if user is logged in, false o.w.
	**/
	private function _is_logged_in() {
		$sess_data = $this->session->all_userdata();
		return array_key_exists("user", $sess_data);
	}

	private function _send_json($data, $code=200) {
		// log vars
		// echo var_dump($data);
		// return;

		// format response
		$this->output
			->set_status_header($code)
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/auth.php */
