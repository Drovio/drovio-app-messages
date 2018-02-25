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
 * @version	5.0-1
 * @created	August 29, 2015, 11:33 (EEST)
 * @updated	September 5, 2015, 15:02 (EEST)
 */
class ChatMessage {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $author;

	/**
	 * The Chat this message belongs to.
	 * 
	 * @type	integer
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
	 * @param	integer	$chat
	 * 		{description}
	 * 
	 * @param	integer	$author
	 * 		{description}
	 * 
	 * @param	string	$content
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public static function newWithAll($chat, $author, $content) {
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
	 * @return	integer
	 * 		{description}
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
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
	 * @param	integer	$author
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withAuthor($author) {
		if ($this->author === null) {
			$this->author = $author;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$chat
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withChat($chat) {
		if ($this->chat === null) {
			$this->chat = $chat;
		}
		
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
		if ($this->content === null) {
			$this->content = $content;
		}
		
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
		if ($this->id === null) {
			$this->id = $id;
		}
		
		return $this;
	}
}
//#section_end#
?>