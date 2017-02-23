<?php
class Users_model extends CI_Model {

	public function __construct() {
		$this->load->database();
	}

	/**
	* Get a page of users.
	*
	* @pre		: `$page` must be a non-negatve int
	* @pre		: `$n` must be a positive int
	* @post		: an array of users will be returned (0 <= length <= $n)
	*
	* @param	: int	: page	: the page of users to retrieve
	* @param	: int	: n		: the number of users to retrieve
	* @return	: array : users
	**/
    public function get_page($page=0, $n=25) {
        // compute offset
        $num_skips = $page * $n;

        // fetch n users with num_skips
        return $this->db
					->get('users', $n, $num_skips)
					->result_array();
    }

	/**
	* Get a specific user by its id
	*
	* @pre		: `$id` must be a valid user id
	* @post		: a query will be made to the database to fetch users
	* @post		: a user object will be returned
	*
	* @param	: int		: id	: the id of the user to retrieve
	* @return	: array 	: user object
	**/
    public function get_user_by_id($id) {
		// get user
		$query = $this->db
					->get_where('users', ['id' => $id], 1, 0);

		if ($query->num_rows() === 0) {
			// exit and return 404
			return [
				"error" => "Cannot find user with id:".$id
			];
		}
		$user = (array)($query->row());

		return $user;
    }

	/**
	* get a specific user by its email
	*
	* @pre		: `$id` must be a valid user id
	* @post		: a query will be made to the database to fetch users
	* @post		: a user object will be returned
	*
	* @param	: string	: email	: the id of the user to retrieve
	* @return	: array 	: user object
	**/
    public function get_user_by_email($email) {
		// get user
		$query = $this->db
					->get_where('users', ['email' => $email], 1, 0);

		if ($query->num_rows() === 0) {
			// exit and return 404
			return [
				"error" => "cannot find user with email:".$email
			];
		}
		$user = (array)($query->row());

		return $user;
    }

	/**
	* Create a new user.
	*
	* @pre		: the given `email` must not be used
	* @post		: [success] a new user record will be created
	* @post 	: [error] an error message will be returned
	*
	* @param	: string	: email	: email
	* @return	: null
	**/
	public function create($email, $password) {
		$user = [
			'email'		=> $email,
			'password'	=> $password
		];

		// insert user and message
		$u = $this->db->insert('users', $user);
		return;
	}

	/**
	* TODO: Remove a user.
	*
	* @pre		: the given `$id` should refer to an existing user record
	* @post		: [success]
	*
	* @param	: Integer	: $id	: the id of the user to delete
	* @return	: null
	**/
	public function remove($id) {
		return;
	}

}
