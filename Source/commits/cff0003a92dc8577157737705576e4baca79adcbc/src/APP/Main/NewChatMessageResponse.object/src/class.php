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
 * Represents the response generated after a NewChatMessageResponse has been processed.
 * 
 * {description}
 * 
 * @version	0.1-2
 * @created	September 29, 2015, 21:08 (EEST)
 * @updated	September 29, 2015, 21:11 (EEST)
 */
class NewChatMessageResponse {

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $chatId;
	
	/**
	 * {description}
	 * 
	 * @type	boolean
	 */
	private $firstMessage;

	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function __construct() {
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat the new message created belongs to.
	 * 
	 * @return	NewChatMessageResponse
	 * 		$this
	 */
	public function buildChatId($chatId) {
		$this->chatId = $chatId;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	boolean	$value
	 * 		A value of "true" indicates that this message is the first of its Chat.
	 * 
	 * @return	NewChatMessageResponse
	 * 		$this
	 */
	public function buildFirstMessage($value) {
		$this->firstMessage = $value;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getChatId() {
		return $this->chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	boolean
	 * 		A value of "true" indicates that this message is the first of its Chat.
	 */
	public function isFirstMessage() {
		return $this->firstMessage;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this response. The array  format is as follows:
	 * 		
	 * 		  array(
	 * 		    'chat_id' => <chat ID>,
	 * 		    'first_message' => <first message>
	 * 		  )
	 */
	public function toArray() {
		return array(
			'chat_id' => $this->getChatId(),
			'first_message' => $this->isFirstMessage()
		);
	}
}
//#section_end#
?>