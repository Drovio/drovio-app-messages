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
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 1, 2015, 16:42 (EEST)
 * @updated	September 1, 2015, 16:42 (EEST)
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
	 * @type	integer
	 */
	private $chatId;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $content;
	
	private $firstMessage;
	
	/**
	 * {description}
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
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$authorId
	 * 		{description}
	 * 
	 * @param	string	$content
	 * 		{description}
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
	 * @return	integer
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
	
	public function isFirstMessage() {
		return $this->firstMessage;
	}
}
//#section_end#
?>