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
 * Represents the response generated after a request for opening a Chat has been processed.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 21:26 (EEST)
 * @updated	September 29, 2015, 21:26 (EEST)
 */
class OpenChatResponse {

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	const KEY_CHAT_ID = 'chat_id';

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
	 * @type	integer
	 */
	private $userAccountId;

	/**
	 * {description}
	 * 
	 * @param	integer	$userAccountId
	 * 		The ID of the currently logged-in user.
	 * 
	 * @return	void
	 */
	public function __construct($userAccountId) {
		$this->content = array();
		$this->userAccountId = $userAccountId;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$chatId
	 * 		{description}
	 * 
	 * @return	OpenChatResponse
	 * 		$this
	 */
	public function buildChat($chatId) {
		if (!isset($this->content[self::KEY_CHAT_ID])) {
			$this->content[self::KEY_CHAT_ID] = $chatId;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$messages
	 * 		The messages to be presented to the user right after the Chat is opened.
	 * 
	 * @return	OpenChatResponse
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
	 * 		    KEY_MESSAGES => <an array of ChatMessage::toArray return values>
	 * 		  )
	 */
	public function toArray() {
		return $this->content;
	}
}
//#section_end#
?>