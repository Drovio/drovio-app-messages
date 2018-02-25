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
application::import('Main', 'CloseChatRequest');

use API\Platform\engine;
use APP\Main\CloseChatRequest;

/**
 * Represents the response generated after a CloseChatRequest has been processed.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:32 (EEST)
 * @updated	September 29, 2015, 20:32 (EEST)
 */
class CloseChatResponse {

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
	 * {description}
	 * 
	 * @param	boolean	$completeClose
	 * 		A value of "true" indicates that the message input box should be removed along with the chat from the front-end.
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat that was requested to be closed.
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
	 * 		The ID of the Chat that was requested to be closed.
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
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this response. The array  format is as follows:
	 * 		
	 * 		  array(
	 * 		    CloseChatRequest::FIELD_NAME_CHAT_ID => <chat ID>,
	 * 		    CloseChatRequest::KEY_COMPLETE_CLOSE => <complete close>
	 * 		  )
	 */
	public function toArray() {
		return array(
			CloseChatRequest::FIELD_NAME_CHAT_ID => $this->getChatId(),
			CloseChatRequest::KEY_COMPLETE_CLOSE => $this->isCompleteClose()
		);
	}
}
//#section_end#
?>