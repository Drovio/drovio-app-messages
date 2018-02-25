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
 * A ChatMessage that needs to be delivered to a ChatParticipant at the next poll cycle.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 9, 2015, 15:49 (EEST)
 * @updated	September 9, 2015, 15:49 (EEST)
 */
class PendingMessage {

	private $author;
	
	private $authorName;
	
	private $content;
	
	private $createdAt;

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $id;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $message;
	
	private $mine;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $participant;

	/**
	 * {description}
	 * 
	 * @param	integer	$message
	 * 		{description}
	 * 
	 * @param	integer	$participant
	 * 		{description}
	 * 
	 * @return	PendingMessage
	 * 		{description}
	 */
	public static function newWithAll($message, $participant) {
		$pm = new PendingMessage();
		$pm->withMessage($message)
			->withParticipant($participant);
			
		return $pm;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	PendingMessage
	 * 		{description}
	 */
	public static function newWithId($id) {
		$pm = new PendingMessage();
		$pm->withId($id);
		
		return $pm;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function getAuthorName() {
		return $this->authorName;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getCreatedAt() {
		return $this->createdAt;
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
	 * @return	integer
	 * 		{description}
	 */
	public function getMessage() {
		return $this->message;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getParticipant() {
		return $this->participant;
	}
	
	public function isMine() {
		return $this->mine;
	}
	
	public function withAuthor($id) {
		if ($this->author === null) {
			$this->author = $id;
		}
		
		return $this;
	}
	
	public function withAuthorName($name) {
		if ($this->authorName === null) {
			$this->authorName = $name;
		}
	
		return $this;
	}
	
	public function withContent($content) {
		if ($this->content === null) {
			$this->content = $content;
		}
		
		return $this;
	}
	
	public function withCreatedAt($createdAt) {
		if ($this->createdAt === null) {
			$this->createdAt = $createdAt;
		}
	
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	PendingMessage
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
	 * @param	integer	$message
	 * 		{description}
	 * 
	 * @return	PendingMessage
	 * 		{description}
	 */
	public function withMessage($message) {
		if ($this->message === null) {
			$this->message = $message;
		}
		
		return $this;
	}
	
	public function withMine($mine) {
		if ($this->mine === null) {
			$this->mine = $mine;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$participant
	 * 		{description}
	 * 
	 * @return	PendingMessage
	 * 		{description}
	 */
	public function withParticipant($participant) {
		if ($this->participant === null) {
			$this->participant = $participant;
		}
	
		return $this;
	}
	
	public function toArray() {
		return array(
			'author' => $this->getAuthor(),
			'author_name' => $this->getAuthorName(),
			'content' => $this->getContent(),
			'created_at' => $this->getCreatedAt(),
			'id' => $this->getId(),
			'message' => $this->getMessage(),
			'mine' => $this->isMine(),
			'participant' => $this->getParticipant()
		);
	}
}
//#section_end#
?>