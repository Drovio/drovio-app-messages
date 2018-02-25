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
 * @version	5.0-1
 * @created	August 29, 2015, 12:13 (EEST)
 * @updated	September 5, 2015, 14:32 (EEST)
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
	 * @type	integer
	 */
	private $owner;
	
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
	 * @return	Chat
	 * 		{description}
	 * 
	 * @throws	\InvalidArgumentException
	 */
	public static function newWithAll($owner, array $recipients, $team) {
//		if (!static::isValid($owner, $recipients, $team)) {
//			throw new \InvalidArgumentException('The owner and the recipients must all be members of the provided team.');
//		}
		
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
	
	public function __construct() {
		$this->recipients = array();
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
		if ($this->id === null) {
			$this->id = $id;
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
	 * @param	{type}	$recipients
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function withRecipients(array $recipients) {
		if (empty($this->recipients)) {
			$this->recipients = $recipients;
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
//	private static function isValid($owner, array $recipients, $team) {
//		if (!$owner->isMemberOf($team)) {
//			return false;
//		}
//		
//		foreach ($recipients as $recipient) {
//			if (!$recipient->isMemberOf($team)) {
//				return false;
//			}
//		}
//		
//		return true;
//	}
}
//#section_end#
?>