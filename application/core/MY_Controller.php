<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	/**
	* Initialize the database classes and class attributes.
	*
	* @post 	: prevents caching
	*
	**/
	public function __construct() {
		parent::__construct();

		// prevent caching
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}

	public function _send_json($data, $code=200) {
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
/* Location: ./application/controllers/welcome.php */
