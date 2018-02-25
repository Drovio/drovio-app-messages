<?php
//#section#[header]
// Namespace
namespace APP\Main;

require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import application loader
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;
//#section_end#
//#section#[class]
/**
 * @library	APP
 * @package	Main
 * 
 * @copyright	Copyright (C) 2015 Messages. All rights reserved.
 */

/**
 * Represents the response generated after a PollMessagesRequest has been processed.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 22:50 (EEST)
 * @updated	September 29, 2015, 22:50 (EEST)
 */
class PollMessagesResponse {

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	const KEY_MESSAGES = 'messages';
	
	/**
	 * {description}
	 * 
	 * @type	array
	 */
	private $content;

	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function __construct() {
		$this->content = array();
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$messages
	 * 		The PendingMessages created for the ChatParticipant of this response.
	 * 
	 * @return	PollMessagesResponse
	 * 		$this
	 */
	public function buildMessages(array $messages) {
		if (isset($this->content[self::KEY_MESSAGES])) {
			return $this;
		}
		
		$normalizedMessages = array();
		foreach ($messages as $message) {
			$normalizedMessages[] = $message->toArray();
		}
		$this->content[self::KEY_MESSAGES] = $normalizedMessages;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this response. The array  format is as follows:
	 * 		
	 * 		  array(
	 * 		    KEY_MESSAGES =&gt; <an array of PendingMessage::toArray return values>
	 * 		  )
	 */
	public function toArray() {
		return $this->content;
	}
}
//#section_end#
?>