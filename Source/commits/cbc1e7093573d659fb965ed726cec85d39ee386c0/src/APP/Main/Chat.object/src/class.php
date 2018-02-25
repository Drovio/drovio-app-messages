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
 * {title}
 * 
 * {description}
 * 
 * @version	7.0-1
 * @created	August 29, 2015, 12:13 (EEST)
 * @updated	September 18, 2015, 21:00 (EEST)
 */
class Chat {

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
	 * @type	integer
	 */
	private $id;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $lastMessageId;

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $owner;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $participants;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $recipients;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $team;
	
	/**
	 * {description}
	 * 
	 * @type	\DateTime
	 */
	private $updated;

	/**
	 * Creates a new Chat were all of its required fields are set.
	 * 
	 * @param	integer	$owner
	 * 		{description}
	 * 
	 * @param	integer	$recipients
	 * 		{description}
	 * 
	 * @param	integer	$team
	 * 		{description}
	 * 
	 * @param	\DateTime	$createdAt
	 * 		{description}
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
	 * Creates a new Chat were only its id is set.
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
	public function getLastMessageId() {
		return $this->lastMessageId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	/**
	 * Returns the IDs of all the participants of this Chat.
	 * 
	 * The difference with the "getRecipients" method is that this method includes the Chat owner in the returned result.
	 * 
	 * @return	integer
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
	 * 		{description}
	 */
	public function getTeam() {
		return $this->team;
	}
	
	public function isActive() {
		return $this->active;
	}
	
	/**
	 * {description}
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	public function isUpdated() {
		return $this->updated;
	}
	
	public function withActive($active) {
		if ($this->active === null) {
			$this->active = $active;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	\DateTime	$createdAt
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withCreatedAt(\DateTime $createdAt) {
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
	 * @return	Chat
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
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withLastMessageId($id) {
		if ($this->lastMessageId === null) {
			$this->lastMessageId = $id;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$owner
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withOwner($owner) {
		if ($this->owner === null) {
			$this->owner = $owner;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$participants
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withParticipants(array $participants) {
		if (empty($this->participants)) {
			$this->participants = $participants;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$team
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withTeam($team) {
		if ($this->team === null) {
			$this->team = $team;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	boolean	$updated
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
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