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
 * @version	6.0-1
 * @created	August 29, 2015, 12:13 (EEST)
 * @updated	September 6, 2015, 17:17 (EEST)
 */
class Chat {

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
	 * @type	\DateTime
	 */
	private $lastMessageAt;

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
	 * @param	ChatParticipant	$owner
	 * 		{description}
	 * 
	 * @param	ChatParticipant[]	$recipients
	 * 		{description}
	 * 
	 * @param	ChatParticipantTeam	$team
	 * 		{description}
	 * 
	 * @param	\DateTime	$createdAt
	 * 		{description}
	 * 
	 * @param	\DateTime	$lastMessageAt
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 * 
	 * @throws	\InvalidArgumentException
	 */
	public static function newWithAll($owner, array $recipients, $team, \DateTime $createdAt, \DateTime $lastMessageAt) {
		$participants = array_merge(array($owner), $recipients);
		
		$chat = new Chat();
		$chat->withOwner($owner)
			->withParticipants($participants)
			->withTeam($team)
			->withCreatedAt($createdAt)
			->withLastMessageAt($lastMessageAt);
			
		return $chat;
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
	 * @return	\DateTime
	 * 		{description}
	 */
	public function getLastMessageAt() {
		return $this->lastMessageAt;
	}
	
	/**
	 * {description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function getParticipants() {
		return $this->participants;
	}
	
	/**
	 * {description}
	 * 
	 * @return	ChatParticipant[]
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
	 * @return	ChatParticipantTeam
	 * 		{description}
	 */
	public function getTeam() {
		return $this->team;
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
	 * @param	\DateTime	$lastMessageAt
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withLastMessageAt(\DateTime $lastMessageAt) {
		if ($this->lastMessageAt === null) {
			$this->lastMessageAt = $lastMessageAt;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	ChatParticipant	$owner
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
	 * @param	{type}	$participants
	 * 		{description}
	 * 
	 * @return	void
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
	 * @param	{type}	$team
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function withTeam($team) {
		if ($this->team === null) {
			$this->team = $team;
		}
		
		return $this;
	}
}
//#section_end#
?>