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
 * @version	4.0-3
 * @created	August 29, 2015, 12:13 (EEST)
 * @updated	September 2, 2015, 17:23 (EEST)
 */
class Chat {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $id;

	/**
	 * {description}
	 * 
	 * @type	ChatParticipant
	 */
	private $owner;
	
	/**
	 * {description}
	 * 
	 * @type	ChatParticipant[]
	 */
	private $recipients;
	
	/**
	 * {description}
	 * 
	 * @type	ChatParticipantTeam
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
	 * @return	Chat
	 * 		{description}
	 * 
	 * @throws	\InvalidArgumentException
	 */
	public static function newWithAll(ChatParticipant $owner, array $recipients, ChatParticipantTeam $team) {
		if (!static::isValid($owner, $recipients, $team)) {
			throw new \InvalidArgumentException('The owner and the recipients must all be members of the provided team.');
		}
		
		$chat = new Chat();
		$chat->withOwner($owner)
			->withRecipients($recipients)
			->withTeam($team);
			
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
	 * @return	integer
	 * 		{description}
	 */
	public function getId() {
		return $this->id;
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
	 * @return	ChatParticipant[]
	 * 		{description}
	 */
	public function getRecipients() {
		return $this->recipients;
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
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withId($id) {
		if ($this->id !== null) {
			return;
		}
		
		$id = intval($id);
		if ($id <= 0) {
			throw new \InvalidArgumentException('The id cannot be an integer smaller than 1.');
		}
		
		$this->id = $id;
		
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
	public function withOwner(ChatParticipant $owner) {
		$this->owner = $owner;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	ChatParticipant[]	$recipients
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withRecipients(array $recipients) {
		$this->recipients = $recipients;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	ChatParticipantTeam	$team
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withTeam(ChatParticipantTeam $team) {
		$this->team = $team;
		
		return $this;
	}
	
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
	 * @return	boolean
	 * 		{description}
	 */
	private static function isValid(ChatParticipant $owner, array $recipients, ChatParticipantTeam $team) {
		if (!$owner->isMemberOf($team)) {
			return false;
		}
		
		foreach ($recipients as $recipient) {
			if (!$recipient->isMemberOf($team)) {
				return false;
			}
		}
		
		return true;
	}
}
//#section_end#
?>