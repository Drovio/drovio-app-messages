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

application::import('Main', 'ChatParticipant');
application::import('Main', 'ChatParticipantTeam');

use \APP\Main\ChatParticipant;
use \APP\Main\ChatParticipantTeam;

/**
 * Represents a conversation among users.
 * 
 * A Chat is initiated by a user. After that this user becomes the Chat's owner.
 * 
 * In addition to this, a Chat can only be held among members of the same team.
 * 
 * Furthermore, only two members can be part of a Chat in the current version of the system.
 * 
 * 
 * Setter Methods Naming Convention
 * 
 * You will notice that the setter methods follow a certain naming convention. That is they are prefixed with the word "with". For example, the "withActive" method.
 * 
 * This pattern is used in order to indicate the fact that the value is set only once, the first time the method is called. Further invocations will not alter the property's value. This effectively makes this property immutable.
 * 
 * @version	9.0-1
 * @created	August 29, 2015, 12:13 (EEST)
 * @updated	September 28, 2015, 18:13 (EEST)
 */
class Chat {

	/**
	 * {description}
	 * 
	 * @type	boolean
	 */
	private $active;

	/**
	 * {description}
	 * 
	 * @type	\DateTime
	 */
	private $createdAt;

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $id;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $lastMessageId;

	/**
	 * The owner's ID.
	 * 
	 * @type	integer
	 */
	private $owner;
	
	/**
	 * The IDs of the participants.
	 * 
	 * @type	array
	 */
	private $participants;
	
	/**
	 * The IDs of the recipients.
	 * 
	 * @type	array
	 */
	private $recipients;
	
	/**
	 * The team's ID.
	 * 
	 * @type	integer
	 */
	private $team;
	
	/**
	 * The last time this Chat has been updated.
	 * 
	 * @type	\DateTime
	 */
	private $updated;

	/**
	 * Creates a new Chat were all of its required fields are set.
	 * 
	 * @param	integer	$owner
	 * 		The owner's ID.
	 * 
	 * @param	array	$recipients
	 * 		The IDs of the recipients.
	 * 		
	 * 		An array of integers.
	 * 
	 * @param	integer	$team
	 * 		The team's ID.
	 * 
	 * @param	\DateTime	$createdAt
	 * 		The time this Chat has been created.
	 * 		
	 * 		It is expected to be in UTC.
	 * 
	 * @return	Chat
	 * 		{description}
	 * 
	 * @throws	\InvalidArgumentException
	 */
	public static function newWithAll($owner, array $recipients, $team, \DateTime $createdAt) {
		$participants = array_merge(array($owner), $recipients);
		
		$chat = new Chat();
		$chat->withOwner($owner)
			->withParticipants($participants)
			->withTeam($team)
			->withCreatedAt($createdAt);
			
		return $chat;
	}
	
	/**
	 * Creates a new Chat were only its ID is set.
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public static function newWithId($id) {
		$chat = new Chat();
		$chat->withId($id);
		
		return $chat;
	}
	
	/**
	 * {description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function __construct() {
		$this->recipients = array();
	}
	
	/**
	 * {description}
	 * 
	 * @return	\DateTime
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
	public function getId() {
		return $this->id;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getLastMessageId() {
		return $this->lastMessageId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		The owner's ID.
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	/**
	 * Returns the IDs of all the participants of this Chat.
	 * 
	 * The difference with the "getRecipients" method is that this method includes the Chat owner in the returned result.
	 * 
	 * @return	array
	 * 		{description}
	 */
	public function getParticipants() {
		return $this->participants;
	}
	
	/**
	 * Returns the IDs of this Chat's recipients.
	 * 
	 * The difference with the "getParticipants" method is that this method does not include the Chat owner in the returned result.
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getRecipients() {
		if ($this->recipients !== null) {
			return $this->recipients;
		}
		
		$participants = $this->getParticipants();
		$index = array_search($this->getOwner(), $participants);
		
		return $this->recipients = $index !== false
			? array_splice($participants, $index, 1)
			: $participants;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		This Chat team's ID.
	 */
	public function getTeam() {
		return $this->team;
	}
	
	/**
	 * A Chat is active if the currently logged-in user has opened it.
	 * 
	 * @return	void
	 */
	public function isActive() {
		return $this->active;
	}
	
	/**
	 * A Chat is updated if at least one of its participants has yet to receive all of the Chat's messages.
	 * 
	 * In other words, a new message has been added to the Chat which one of its participants has not received it yet.
	 * 
	 * @return	boolean
	 * 		Returns "true" if this Chat has been updated.
	 * 
	 * @deprecated	This mechanism has been replaced by a pending message queue.
	 */
	public function isUpdated() {
		return $this->updated;
	}
	
	/**
	 * Initializes the "active" state of this Chat.
	 * 
	 * @param	boolean	$active
	 * 		See method "isActive" for the definition of an active Chat.
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withActive($active) {
		if ($this->active === null) {
			$this->active = $active;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the creation time of this Chat.
	 * 
	 * @param	\DateTime	$createdAt
	 * 		The time is expected to be in UTC.
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withCreatedAt(\DateTime $createdAt) {
		if ($this->createdAt === null) {
			$this->createdAt = $createdAt;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the ID of this Chat.
	 * 
	 * @param	string	$id
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withId($id) {
		if ($this->id === null) {
			$this->id = $id;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the ID of the last message of this Chat.
	 * 
	 * @param	string	$id
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withLastMessageId($id) {
		if ($this->lastMessageId === null) {
			$this->lastMessageId = $id;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the owner's ID.
	 * 
	 * @param	integer	$owner
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withOwner($owner) {
		if ($this->owner === null) {
			$this->owner = $owner;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the participants' IDs.
	 * 
	 * @param	array	$participants
	 * 		An array of integers.
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withParticipants(array $participants) {
		if (empty($this->participants)) {
			$this->participants = $participants;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the team's ID.
	 * 
	 * @param	integer	$team
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		$this
	 */
	public function withTeam($team) {
		if ($this->team === null) {
			$this->team = $team;
		}
		
		return $this;
	}
	
	/**
	 * Initializes the "updated" state of this Chat.
	 * 
	 * See the "isUpdated" method for the definition of an updated Chat.
	 * 
	 * @param	boolean	$updated
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		Initializes t
	 */
	public function withUpdated($updated) {
		if ($this->updated === null) {
			$this->updated = $updated;
		}
		
		return $this;
	}
}
//#section_end#
?>