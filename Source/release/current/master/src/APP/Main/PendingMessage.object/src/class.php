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
 * A ChatMessage that needs to be delivered to a ChatParticipant at the next Polling Cycle.
 * 
 * Message Polling Mechanism
 * 
 * The Polling Cycle is part of the Message Polling Mechanism. This mechanism refers to the process utilized by the system in order to allow the live interaction among the ChatParticipants.
 * 
 * It is divided into two parts:
 * 
 *  1. the creation and
 *  2. the delivery
 * 
 * of PendingMessages.
 * 
 * The creation process is the following:
 * 
 *  1. The user creates a new ChatMessage.
 *  2. The system saves the ChatMessage and creates PendingMessages for each ChatParticipant including the ChatMessage's author. These PendingMessages are references to the original ChatMessage.
 * 
 * The delivery process comes after the creation and includes the following steps:
 * 
 *  1. The client asks the server for any PendingMessages that should be delivered to the ChatParticipant.
 *  2. The server returns these PendingMessages to the client.
 *  3. The client presents the PendingMessages and sets up a reminder to ask again after a particular time period has passed (for example, 5 seconds).
 * 
 * Steps 1 through 3 above comprise the Polling Cycle.
 * 
 * 
 * Referenced Message
 * 
 * Some of the methods of this class return information that is available in the ChatMessage class, but, nonetheless, presented here. That is for ease of reference purposes. The "getAuthor" method is an example of such a method.
 * 
 * In order to simplify the documentation of these methods, we will refer to the ChatMessage that this PendingMessage refers to as the "Referenced Message".
 * 
 * 
 * Setter Methods Naming Convention
 * 
 * See the "Setter Methods Naming Convention" note in the Chat class description.
 * 
 * @version	1.0-2
 * @created	September 9, 2015, 15:49 (EEST)
 * @updated	September 29, 2015, 22:39 (EEST)
 */
class PendingMessage {

	/**
	 * The author's ID.
	 * 
	 * @type	integer
	 */
	private $author;
	
	/**
	 * The URL of the author's avatar.
	 * 
	 * @type	string
	 */
	private $authorAvatar;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $authorName;
	
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
	 * The ID of the ChatMessage this PendingMessage refers to.
	 * 
	 * @type	string
	 */
	private $message;
	
	/**
	 * {description}
	 * 
	 * @type	boolean
	 */
	private $mine;
	
	/**
	 * The ChatParticipant's account ID.
	 * 
	 * @type	integer
	 */
	private $participant;

	/**
	 * Creates a new PendingMessage were all of its required fields are set.
	 * 
	 * @param	string	$message
	 * 		The ID of the ChatMessage this PendingMessage refers to.
	 * 
	 * @param	integer	$participant
	 * 		The ChatParticipant's account ID.
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
	 * 
	 * @deprecated	Use newWithAll instead.
	 */
	public static function newWithId($id) {
		$pm = new PendingMessage();
		$pm->withId($id);
		
		return $pm;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The Referenced Message author's ID.
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The URL of the Referenced Message author's avatar.
	 */
	public function getAuthorAvatar() {
		return $this->authorAvatar;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The Referenced Message author's name.
	 */
	public function getAuthorName() {
		return $this->authorName;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The Referenced Message's content.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The Referenced Message's creation time.
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 * 
	 * @deprecated	The ID field is no longer used. A composite primary key is used instead.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The Referenced Message's ID.
	 */
	public function getMessage() {
		return $this->message;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		The account ID of the ChatParticipant this PendingMessage was created for.
	 */
	public function getParticipant() {
		return $this->participant;
	}
	
	/**
	 * {description}
	 * 
	 * @return	boolean
	 * 		Returns the "mine" state of the Referenced Message.
	 * 		
	 * 		See the ChatMessage::isMine method.
	 */
	public function isMine() {
		return $this->mine;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		The Referenced Message author's ID.
	 * 
	 * @return	PendingMessage
	 * 		$this
	 */
	public function withAuthor($id) {
		if ($this->author === null) {
			$this->author = $id;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$avatar
	 * 		The URL of the Referenced Message author's avatar.
	 * 
	 * @return	PendingMessage
	 * 		$this
	 */
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
	 * 		The Referenced Message author's name.
	 * 
	 * @return	PendingMessage
	 * 		$this
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
	 * @param	string	$content
	 * 		The Referenced Message's content.
	 * 
	 * @return	PendingMessage
	 * 		$this
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
	 * @param	string	$createdAt
	 * 		The Referenced Message's creation time.
	 * 
	 * @return	PendingMessage
	 * 		$this
	 */
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
	 * 		$this
	 * 
	 * @deprecated	See the "getId" method.
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
	 * @param	string	$message
	 * 		The Referenced Message's ID.
	 * 
	 * @return	PendingMessage
	 * 		$this
	 */
	public function withMessage($message) {
		if ($this->message === null) {
			$this->message = $message;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	boolean	$mine
	 * 		Returns the "mine" state of the Referenced Message.
	 * 		
	 * 		See the ChatMessage::isMine method.
	 * 
	 * @return	PendingMessage
	 * 		$this
	 */
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
	 * 		The account ID of the ChatParticipant this PendingMessage was created for.
	 * 
	 * @return	PendingMessage
	 * 		$this
	 */
	public function withParticipant($participant) {
		if ($this->participant === null) {
			$this->participant = $participant;
		}
	
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this PendingMessage. The form of the array is the same as the ChatMessage::toArray with the addition of the following two keys:
	 * 		
	 * 		 - 'message' => <the Referenced Message's ID> : string
	 * 		 - 'participant' => <the account ID of the ChatParticipant this PendingMessage was created for> : integer
	 */
	public function toArray() {
		return array(
			'author' => $this->getAuthor(),
			'author_avatar' => $this->getAuthorAvatar(),
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