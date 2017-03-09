<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

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
	}

	/**
	* Retrieve a user.
	*
	* @post : returns a JSON object with user details and messages
	*
	* @param	: int		: id	: the specific page of users to load
	* @return	: array 	: object with user and its messages if it exists
	**/
	public function index($id) {
		// fetch user
		$data = $this->users_model->get_user_by_id($id);
		return $this->_send_json($data);
	}

	/**
	* Update a user's email or password.
	*
	* @post : returns a JSON object with user details and messages
	*
	* @param	: int		: id	: the specific page of users to load
	* @return	: array 	: object with user and its messages if it exists
	**/
	public function update() {
		// TODO: code in here
	}

	/**
	* Remove a user.
	*
	* @post : returns a JSON object with user details and messages
	*
	* @param	: int		: id	: the specific page of users to load
	* @return	: array 	: object with user and its messages if it exists
	**/
	public function remove($id) {
		if ($this->_is_admin() || $this->get_user()['id'] === (int)$id) {
			$this->users_model->remove((int)$id);
			return $this->_send_json(array(
				"error" => false,
				"errorMessage" => ""
			));
		}
		return $this->_send_json(array(
			"error" => true,
			"errorMessage" => "You do not have the permission to remove this user."
		), 403);
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
