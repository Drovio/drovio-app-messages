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

application::import('Main', 'Chat');
application::import('Main', 'ChatParticipant');

use \APP\Main\Chat;
use \APP\Main\ChatParticipant;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	4.0-1
 * @created	August 29, 2015, 11:33 (EEST)
 * @updated	September 2, 2015, 17:31 (EEST)
 */
class ChatMessage {

	/**
	 * {description}
	 * 
	 * @type	ChatParticipant
	 */
	private $author;

	/**
	 * The Chat this message belongs to.
	 * 
	 * @type	Chat
	 */
	private $chat;

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $content;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $id;
	
	/**
	 * {description}
	 * 
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @param	ChatParticipant	$author
	 * 		{description}
	 * 
	 * @param	string	$content
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public static function newWithAll(Chat $chat, ChatParticipant $author, $content) {
		$message = new ChatMessage();
		$message->withChat($chat)
			->withAuthor($author)
			->withContent($content);
			
		return $message;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public static function newWithId($id) {
		$message = new ChatMessage();
		$message->withId($id);
		
		return $message;
	}
	
	/**
	 * {description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	/**
	 * {description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function getChat() {
		return $this->chat;
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
	 * @return	integer
	 * 		{description}
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * {description}
	 * 
	 * @param	ChatParticipant	$author
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withAuthor(ChatParticipant $author) {
		$this->author = $author;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withChat(Chat $chat) {
		$this->chat = $chat;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$content
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withContent($content) {
		$this->content = $content;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withId($id) {
		if ($this->id !== null) {
			return $this;
		}
		
		$this->id = $id;
		
		return $this;
	}
}
//#section_end#
?>