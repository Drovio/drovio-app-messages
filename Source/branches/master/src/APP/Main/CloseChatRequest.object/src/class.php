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

importer::import('API', 'Platform', 'engine');
 
use API\Platform\engine;

/**
 * Represents a request for closing a Chat.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:28 (EEST)
 * @updated	September 29, 2015, 20:28 (EEST)
 */
class CloseChatRequest {
 
 	/**
 	 * {description}
 	 * 
 	 * @type	string
 	 */
 	const KEY_COMPLETE_CLOSE = 'complete_close';

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	const FIELD_NAME_CHAT_ID = 'diomsg-chat-id';
 
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
 	private $completeClose;

 	/**
 	 * Creates a new CloseChatRequest with data retrieved from the "engine".
 	 * 
 	 * @return	CloseChatRequest
 	 * 		{description}
 	 */
 	public static function fromEngine() {
		$completeClose = (bool) engine::getVar(self::KEY_COMPLETE_CLOSE);
		$chatId = engine::getVar(self::FIELD_NAME_CHAT_ID);
	
		return new CloseChatRequest($completeClose, $chatId);
	}

	/**
	 * {description}
	 * 
	 * @param	boolean	$completeClose
	 * 		A value of "true" indicates that the message input box should be removed along with the chat from the front-end.
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat to be closed.
	 * 
	 * @return	void
	 */
	public function __construct($completeClose, $chatId) {
		$this->completeClose = $completeClose;
		$this->chatId = $chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The ID of the Chat to be closed.
	 */
	public function getChatId() {
		return $this->chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	boolean
	 * 		See the constructor for the definition of a "completely closed" chat.
	 */
	public function isCompleteClose() {
		return $this->completeClose;
	}
}
//#section_end#
?>