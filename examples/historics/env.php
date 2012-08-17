<?php
	if (function_exists('date_default_timezone_set')) {
		date_default_timezone_set('UTC');
	}

	// Include the DataSift library
	require dirname(__FILE__).'/../../lib/datasift.php';

	/**
	 * This class is used by the Historics examples to remove the noise of
	 * dealing with command line arguments.
	 */
	class Env {
		public $user = null;
		public $args = array();
	
		public function __construct($args = false)
		{
			// If no args were passed, use the command line args
			if ($args === false) {
				$args = $_SERVER['argv'];
				// Drop the script name
				array_shift($args);
			}

			// Make sure we have credentials on the command line
			if (count($args) < 2) {
				die('Please specify your DataSift username and API key as the first two command line arguments!'.PHP_EOL);
			}
			
			try {
				$username = array_shift($args);
				$api_key = array_shift($args);
				$this->user = new DataSift_User($username, $api_key);
			} catch (Exception $e) {
				die('Failed to create the DataSift_User object - check your username and API key!'.PHP_EOL);
			}
			
			$this->args = $args;
		}
		
		public function displayHistoricDetails($hist)
		{
			echo 'Playback ID: '.$hist->getHash().PHP_EOL;
			echo 'Stream hash: '.$hist->getStreamHash().PHP_EOL;
			echo 'Name:        '.$hist->getName().PHP_EOL;
			echo 'Start time:  '.date('r', $hist->getStartDate()).PHP_EOL;
			echo 'End time:    '.date('r', $hist->getEndDate()).PHP_EOL;
			$sources = $hist->getSources();
			echo 'Source'.(count($sources) == 1 ? ': ' : 's:').'     '.implode(', ', $sources).PHP_EOL;
			echo 'Sample:      '.$hist->getSample().PHP_EOL;
			echo 'Created at:  '.(is_null($hist->getCreatedAt()) ? 'null' : date('r', $hist->getCreatedAt())).PHP_EOL;
			echo 'Status:      '.$hist->getStatus().PHP_EOL;
		}
	}