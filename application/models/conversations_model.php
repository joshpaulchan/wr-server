<?php
class Conversations_model extends CI_Model {

	public function __construct() {
		$this->load->database();
	}

	/**
	* Get a page of conversations.
	*
	* @pre		: `$page` must be a non-negatve int
	* @pre		: `$n` must be a positive int
	* @post		: an array of conversations will be returned (0 <= length <= $n)
	*
	* @param	: int	: $page	: the page of conversations to retrieve
	* @param	: int	: $n	: the number of conversations to retrieve
	* @return	: array : conversations
	**/
    public function get_page($page=0, $n=25) {
        // compute offset
        $num_skips = $page * $n;

        // fetch n conversations with num_skips
        return $this->db
					->get('conversations', $n, $num_skips)
					->result_array();
    }

	/**
	* Get a specific conversation and its messages.
	*
	* @pre		: `$id` must be a valid conversation id
	* @post		: a query will be made to the database to fetch conversations and messages
	* @post		: a conversation object will be returned
	*
	* @param	: int		: $id	: the id of the conversation to retrieve
	* @return	: object 	: conversation object
	**/
    public function get_conversation($id) {
		// get conversation
		$conversations = $this->db
					->get('conversations')
					->where('id', $id)
					->limit(1, 0)
					->result_array();

		if (count($conversations) === 0) {
			// exit and return 404
			return [
				"error" => "Cannot find conversation the id:".$id
			];
		}

		// get conversation messages
		$messages = $this->db
						->get('messages')
						->where('conversation_id', $id)
						->result_array();
		$conversations['messages'] = $messages;

		return $conversations;
    }

	/**
	* TODO: Create a new conversation  and message.
	*
	* @pre		:
	* @post		:
	*
	* @param	:
	* @return	: null
	**/
	public function create() {
		$conversation = [

		];

		// insert conversation and message
		$convo = $this->db->insert('conversations', $conversation);

		$message = [

		];

		$this->db->insert('messages', $message);
		return;
	}

    /**
    * Counts the number of records in the database table.
    *
    * @return   : int   : the number of conversation records in the database.
    **/
    public function count() {
        return $this->db->count_all('conversations');
    }

    public function _seed() {

    }
}
