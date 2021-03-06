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
		// Thanks @Jani Hartikainen for the array-map-using-method solution (http://stackoverflow.com/questions/1077491/can-a-method-be-used-as-a-array-map-function-in-php-5-2)
        return array_map(array($this, "_format_user_object"), $this->db
					->get('users', $n, $num_skips)
					->result_array());
    }

	/**
	* Get a page of users with specific approval.
	*
	* @pre		: `$page` must be a non-negatve int
	* @pre		: `$approval` must be a boolean
	* @pre		: `$n` must be a positive int
	* @post		: an array of users will be returned (0 <= length <= $n)
	*
	* @param	: bool	: approval	: the type of approval status to filter for
	* @param	: int	: page		: the page of users to retrieve
	* @param	: int	: n			: the number of users to retrieve
	* @return	: array : array of user objects
	**/
    public function get_page_with_approval($approval=true, $page=0, $n=25) {
        // compute offset
        $num_skips = $page * $n;

        // fetch n users with num_skips
		// Thanks @Jani Hartikainen for the array-map-using-method solution (http://stackoverflow.com/questions/1077491/can-a-method-be-used-as-a-array-map-function-in-php-5-2)
        return array_map(array($this, "_format_user_object"), $this->db
				->get_where('users', array('approved' => (bool)$approval), $n, $num_skips)
				->result_array());
    }

	/**
	* Get a specific user by her id.
	*
	* @pre		: `$id` must be a valid user id
	* @post		: a query will be made to the database to fetch users
	* @post		: a user object will be returned
	*
	* @param	: int		: id	: the id of the user to retrieve
	* @param	: bool		: raw	: whether to return user straight from DB or clean it
	* up, default false
	* @return	: array 	: user object
	**/
    public function get_user_by_id($id, $raw=false) {
		// get user
		$query = $this->db
					->get_where('users', array('id' => $id), 1, 0);

		if ($query->num_rows() === 0) {
			// exit and return 404
			return array("error" => "Cannot find user with id: ".$id);
		}
		$user = (array)($query->row());

		if ($raw === true) {
			return $user;
		} else {
			return $this->_format_user_object($user);
		}
    }

	/**
	* get a specific user by its email
	*
	* @pre		: `$id` must be a valid user id
	* @post		: a query will be made to the database to fetch users
	* @post		: a user object will be returned
	*
	* @param	: string	: email	: the id of the user to retrieve
	* @param	: bool		: raw	: whether to return user straight from DB or clean it
	* up, default false
	* @return	: array 	: user object
	**/
    public function get_user_by_email($email, $raw=false) {
		// get user
		$query = $this->db
					->get_where('users', array('email' => $email), 1, 0);

		if ($query->num_rows() === 0) {
			// exit and return 404
			return array("error" => "cannot find user with email: ".$email);
		}
		$user = (array)($query->row());

		if ($raw === true) {
			return $user;
		} else {
			return $this->_format_user_object($user);
		}
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
		$user = array(
			'email'		=> $email,
			'password'	=> $password,
			'createdAt'	=> date('Y-m-d H:i:s')
		);

		// insert user and message
		$u = $this->db->insert('users', $user);
		return;
	}

	/**
	* Remove a user.
	*
	* @pre		: the given `$id` should refer to an existing user record
	* @post		: [success] the given user object will be removed
	*
	* @param	: int	: $id	: the id of the user to delete
	* @return	: null
	**/
	public function remove($id) {
		$this->db->delete('users', array('id' => $id));
		return;
	}

	/**
	* Approve a user.
	*
	* @pre		: the given `$id` should refer to an existing user record
	* @post		: [success] the user's `approved` attribute will be set to true
	*
	* @param	: int	: $id	: the id of the user to approve
	* @return	: null
	**/
	public function approve($id) {
		// update user `approved` =  true  if they have the given id
		$this->db->update('users', array('approved' => true), array('id' => $id));
		return;
	}

	/**
	* Set the admin status of a user.
	*
	* @pre		: the given `$id` should refer to an existing user record
	* @post		: [success] the user's `admin` attribute may be modified
	*
	* @param	: int	: $id	: the id of the user to modify
	* @return	: null
	**/
	public function set_admin($id, $status) {
		// update user `admin` attribute
		$this->db->update('users', array('admin' => (bool)$status), array('id' => $id));
		return;
	}

	/**
    * Counts the number of records in the database table with the given approval attribute.
    *
	* @param	: bool	: approval	: the approval status to filter for
    * @return   : int   : the number of conversation records in the database.
    **/
    public function count($approval=true) {
        return $this->db
			->where('approved', (bool)$approval)
			->count_all_results('users');
    }

	/**
	* Format the user object for retrieval.
	*
	* @pre		: the given `$user` should be an associative array
	* @post		: the given `$user` will not be modified
	*
	* @param	: array 	: user	: the user object to format
	* @return	: array 	: the formatted user object
	**/
	private function _format_user_object($user) {
		return array_merge(
			$user,
			array(
				"id" => (int)$user["id"],
				"admin" => (bool)$user["admin"],
				"approved" => (bool)$user["approved"]
			)
		);
	}

}
