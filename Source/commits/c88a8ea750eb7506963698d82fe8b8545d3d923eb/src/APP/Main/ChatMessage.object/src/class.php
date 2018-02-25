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
 * @version	7.0-1
 * @created	August 29, 2015, 11:33 (EEST)
 * @updated	September 18, 2015, 22:46 (EEST)
 */
class ChatMessage {

	/**
	 * The author's ID.
	 * 
	 * @type	integer
	 */
	private $author;
	
	private $authorAvatar;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $authorName;

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
	 * @type	string
	 */
	private $createdAt;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $id;
	
	/**
	 * "true" if this message belongs to the currently logged-in user.
	 * 
	 * @type	boolean
	 */
	private $mine;
	
	/**
	 * Creates a new ChatMessage were all of its required fields are set.
	 * 
	 * @param	integer	$chat
	 * 		The Chat's ID.
	 * 
	 * @param	integer	$author
	 * 		The author's ID.
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
	 * 		The author's ID.
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	public function getAuthorAvatar(){
		return $this->authorAvatar;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getAuthorName() {
		return $this->authorName;
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
	public function getCreatedAt() {
		return $this->createdAt;
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
	 * @return	boolean
	 * 		"true" if the message belongs to the currently logged-in user.
	 */
	public function isMine() {
		return $this->mine;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this ChatMessage. The form of the array is:
	 * 		 array(
	 * 		   'author' => author's ID : integer,
	 * 		   'author_name' => string,
	 * 		   'content' => string,
	 * 		   'created_at' => string,
	 * 		   'id' => integer,
	 * 		   'mine' => boolean
	 * 		 )
	 */
	public function toArray() {
		return array(
			'author' => $this->getAuthor(),
			'author_avatar' => $this->getAuthorAvatar(),
			'author_name' => $this->getAuthorName(),
			'content' => $this->getContent(),
			'created_at' => $this->getCreatedAt(),
			'id' => $this->getId(),
			'mine' => $this->isMine()
		);
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$author
	 * 		The author's ID.
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
	
	public function withAuthorAvatar($avatar) {
		if ($this->authorAvatar === null) {
			$this->authorAvatar = $avatar;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$name
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withAuthorName($name) {
		if ($this->authorName === null) {
			$this->authorName = $name;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$chat
	 * 		The chat's ID.
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
	 * @param	string	$time
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withCreatedAt($time) {
		if ($this->createdAt === null) {
			$this->createdAt = $time;
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
	
	/**
	 * {description}
	 * 
	 * @param	boolean	$mine
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function withMine($mine) {
		if ($this->mine === null) {
			$this->mine = $mine;
		}
		
		return $this;
	}
}
//#section_end#
?>