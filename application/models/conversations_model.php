<?php
class Conversations_model extends CI_Model {

	public function __construct() {
		$this->load->database();
	}

    public function get_page($page=0, $n=0) {
        // compute offset
        $num_skips = $page * $n;

        // fetch n conversations with num_skips
        $query = $this->db->get('conversations', $n, $num_skips);
        return $query->result_array();
    }

    public function get_conversation($id) {
		return [];
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
