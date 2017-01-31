<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// import faker
require_once __DIR__.'/../third_party/fzaninotto/faker/src/autoload.php';

class Seed extends CI_Controller {

	/**
	* Initialize the database classes and class attributes.
	*
	* @post 	: prevents caching
	*
	**/
	public function __construct() {
		parent::__construct();

		$this->load->model('conversations_model');
		$this->load->helper('date');
	}

	/**
	* Seeds the conversation and messages tables.
	*
	* @pre		: `$n` must be a positive integer
	* @pre		: `$drop` must be a boolean value
	* @post		: if `$drop` is truthy, the conversations and messages tables will be dropped prior to seeding
	*
	* @param	: int		: $n	: the number of records to seed
	* @param	: boolean	: $drop	: whether or not to drop tables prior to seeding
	* @return	: null
	**/
	public function conversations($n=300, $drop=false) {

		if ($drop == true) {
			// drop the conversations and messages tables
			$this->db->truncate('conversations');
			$this->db->truncate('messages');
			echo "Dropped `conversations` and `messages` table.".PHP_EOL;
		}

		// create faker
		$faker = Faker\Factory::create();

		for ($i=0; $i < $n; $i++) {
			// create a conversation
			$unread = $faker->boolean;
			$email = $faker->email;
			$createdAt = $faker->dateTimeThisDecade;
			$this->db->insert('conversations', [
				'emailFrom'		=> $email,
				'subject'		=> $faker->catchPhrase,
				'unread'		=> (int)($unread),
				'unreplied'		=> (int)($faker->boolean & $unread),
				'location'		=> $faker->randomElement([ 'inbox', 'spam', 'trash']),
				'userAgent'		=> $faker->userAgent,
				'browser'		=> $faker->randomElement([ 'Chrome', 'Firefox', 'Safari', 'IE']),
				'os'			=> $faker->randomElement([ 'Windows', 'Linux', 'MacOS']),
				'ip'			=> $faker->ipv4,
				'referrer'		=> 'http://rutgers.edu/'.$faker->slug,
			]);

			$convo_id = $this->db->insert_id();
			$num_messages = 3;
			for ($x=0; $x < $num_messages; $x++) {
				// insert messages

				$this->db->insert('messages', [
					'emailFrom'			=> $faker->randomElement([ $email, 'us' ]),
					'emailTo'			=> $faker->randomElement([ $email, 'us' ]),
					'body'				=> $faker->paragraph,
					'conversation_id'	=> $convo_id
				]);
			}
		}

		echo "Seeded ".$n." conversations".PHP_EOL;
	}

	public function message($to="World") {
		echo "Hello, ".$to.PHP_EOL;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
