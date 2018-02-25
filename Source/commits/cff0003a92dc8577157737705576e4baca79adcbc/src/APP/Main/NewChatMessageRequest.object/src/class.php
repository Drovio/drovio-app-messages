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

importer::import("API", "Platform", "engine");
application::import("Main", "ChatMessageFormBuilder");

use API\Platform\engine;
use APP\Main\ChatMessageFormBuilder;

/**
 * Represents a request for creating a new ChatMessage.
 * 
 * {description}
 * 
 * @version	1.0-2
 * @created	September 1, 2015, 16:42 (EEST)
 * @updated	September 29, 2015, 21:09 (EEST)
 */
class NewChatMessageRequest {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $authorId;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $chatId;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $content;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $firstMessage;
	
	/**
	 * Creates a new NewChatMessageRequest with data retrieved from the "engine".
	 * 
	 * @return	NewChatmessageRequest
	 * 		{description}
	 */
	public static function fromEngine() {
		$chatId = engine::getVar(ChatMessageFormBuilder::FIELD_NAME_CHAT_ID);
		$authorId = engine::getVar(ChatMessageFormBuilder::FIELD_NAME_AUTHOR_ID);
		$content = engine::getVar(ChatMessageFormBuilder::FIELD_NAME_MESSAGE);
		$firstMessage = intval(engine::getVar(ChatMessageFormBuilder::FIELD_NAME_FIRST_MESSAGE)) === 1 ? true : false;
		
		return new NewChatMessageRequest($chatId, $authorId, $content, $firstMessage);
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat the new message belongs to.
	 * 
	 * @param	integer	$authorId
	 * 		The author's ID.
	 * 
	 * @param	string	$content
	 * 		The contents of the message.
	 * 
	 * @param	boolean	$firstMessage
	 * 		Whether this is the first message of the Chat or not.
	 * 
	 * @return	void
	 */
	public function __construct($chatId, $authorId, $content, $firstMessage) {
		$this->chatId = $chatId;
		$this->authorId = $authorId;
		$this->content = $content;
		$this->firstMessage = $firstMessage;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getAuthorId() {
		return $this->authorId;
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
	 * @return	string
	 * 		{description}
	 */
	public function getContent() {
		return $this->content;
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
}
//#section_end#
?>