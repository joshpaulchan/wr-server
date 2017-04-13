<?php
class Conversations_model extends CI_Model {

	public function __construct() {
		$this->load->database();

		// load config
		$this->config->load('wr_config');
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
        return array_map(array($this, "_format_conversation_object"), $this->db
					->order_by('createdAt', 'desc')
					->get('conversations', $n, $num_skips)
					->result_array());
    }

	/**
	* Get a specific conversation and its messages.
	*
	* @pre		: `$id` must be a valid conversation id
	* @post		: a query will be made to the database to fetch conversations and messages
	* @post		: a conversation object will be returned
	*
	* @param	: int		: $id	: the id of the conversation to retrieve
	* @return	: array 	: conversation object
	**/
    public function get_conversation($id) {
		// get conversation
		$query = $this->db
					->get_where('conversations', array('id' => $id), 1, 0);

		if ($query->num_rows() === 0) {
			// exit and return 404
			return array(
				"error" => "Cannot find conversation the id:".$id
			);
		}
		$conversation = (array)($query->row());

		// get conversation messages
		$messages = $this->db
						->get_where('messages', array('conversation_id' => $id))
						->result_array();
		$conversation['messages'] = array_map(array($this, "_format_message_object"), $messages);

		return $this->_format_conversation_object($conversation);
    }

	/**
	* Create a new message.
	*
	* @pre		: `$id` must be a valid conversation id
	* @post		: a query will be made to the database to fetch conversations and messages
	* @post		: a conversation object will be returned
	*
	* @param	: int		: $id	: the id of the conversation to retrieve
	* @return	: array 	: conversation object
	**/
	public function create_reply($id, $sentTo, $sentBy, $body) {
		// get conversation messages
		$message = array(
			'emailFrom'			=> $sentBy,
			'emailTo'			=> $sentTo,
			'body'				=> $body,
			'conversation_id'	=> $id,
			'createdAt'			=> date('Y-m-d H:i:s')
		);

		if ($this->db->insert('messages', $message)) {
			return $this->get_conversation($id);
		} else {
			return array(
				"error" => true,
				"message" => "Error saving reply to the database."
			);
		}
	}

	/**
	* Create a new conversation  and message.
	*
	* @post		: creates a new conversation record and initial message record, and links the two
	*
	* @param	: str	: $sentBy	: the email of the person who started the conversation
	* @param	: str	: $subject	: the subject of the conversation
	* @param	: str	: $body		: the message body
	* @param	: str	: $data		: the technical info of the conversation
	* @return	: null
	**/
	public function create($sentBy, $subject, $body, $data) {
		$conversation = array(
			'emailFrom'		=> $sentBy,
			'subject'		=> $subject,
			'unread'		=> true,
			'unreplied'		=> true,
			'location'		=> 'inbox',
			'userAgent'		=> $data['userAgent'],
			'browser'		=> $data['browser'],
			'os'			=> $data['os'],
			'ip'			=> $data['ip'],
			'referrer'		=> $data['referrer'],
			'createdAt'		=> date('Y-m-d H:i:s')
		);

		// insert conversation and message
		$convo = $this->db->insert('conversations', $conversation);
		$convo_id = $this->db->insert_id();

		$res = $this->create_reply(
			$convo_id,
			$this->config->item('webresponse_email'),
			$sentBy,
			$body
		);

		if (array_key_exists("error", $res)) {
			return array(
				"error" => true,
				"message" => "Error saving conversation to the database."
			);
		} else {
			return $res;
		}
	}

	/**
	* Format the given conversation object for more accurate data types.
	*
	* @pre		: the given `$c` should be an associative array
	* @post		: the given `$c` will not be modified.
	* @post		: a transformed copy of the given `$c` will be returned.
	*
	* @param	: array 	: c	: the conversation object to format
	* @return	: array 	: the formatted conversation object
	**/
	private function _format_conversation_object($c) {
		return array_merge(
			$c,
			array(
				"id" => (int)$c["id"],
				"unread" => (bool)$c["unread"],
				"unreplied" => (bool)$c["unreplied"],
			)
		);
	}

	/**
	* Format the given message object for more accurate data types.
	*
	* @pre		: the given `$m` should be an associative array
	* @post		: the given `$m` will not be modified.
	* @post		: a transformed copy of the given `$m` will be returned.
	*
	* @param	: array 	: m	: the message object to format
	* @return	: array 	: the formatted message object
	**/
	private function _format_message_object($m) {
		return array_merge(
			$m,
			array(
				"id" => (int)$m["id"],
				"conversation_id" => (int)$m["conversation_id"],
			)
		);
	}

    /**
    * Counts the number of records in the database table.
    *
    * @return   : int   : the number of conversation records in the database.
    **/
    public function count() {
        return $this->db->count_all('conversations');
    }
}
