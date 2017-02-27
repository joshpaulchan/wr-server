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
	}

	/**
	* Attempt to log into web response.
	*
	* @post : returns a JSON object with links to next queries/pages and pages themselves
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

		if (array_key_exists('errror', $data)) {
			return $this->_send_json([
				'error' => true,
				'message' => "Error logging user in."
			]);
		}

		// Check password
		if (!$this->_passwords_match($pw, $data['password'])) {
			return $this->_send_json([
				'error' => true,
				'message' => 'Password is incorrect.'
			]);
		}

		// TODO: add user to session

		$this->_send_json($data);
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
			]);
		}

		// create new user
		$u = $this->users_model->create($email, $this->pwHasher->HashPassword($pw));

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

	private function _send_json($data) {
		// log vars
		// echo var_dump($data);
		// return;

		// format response
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/auth.php */
