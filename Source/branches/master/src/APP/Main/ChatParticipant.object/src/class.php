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
application::import('Main', 'ChatParticipantTeam');

use \APP\Main\Chat;
use \APP\Main\ChatParticipantTeam;

/**
 * Represents a participant of a Chat.
 * 
 * See the "Setter Methods Naming Convention" note in the Chat class description.
 * 
 * Also, see the Chat::getParticipants method for the definition of a Chat participant.
 * 
 * @version	6.0-1
 * @created	August 29, 2015, 11:57 (EEST)
 * @updated	September 29, 2015, 20:22 (EEST)
 */
class ChatParticipant {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $accountId;
	
	/**
	 * The URL of this participant's avatar.
	 * 
	 * @type	string
	 */
	private $avatar;

	/**
	 * The ID of the Chat this participant is member of.
	 * 
	 * @type	string
	 */
	private $chat;

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
	private $name;
	
	/**
	 * An array of integers with the IDs of the teams this participant belongs to.
	 * 
	 * @type	array
	 */
	private $teams;

	/**
	 * Creates a new ChatParticipant were all of its required fields are set.
	 * 
	 * @param	integer	$accountId
	 * 		{description}
	 * 
	 * @param	string	$name
	 * 		{description}
	 * 
	 * @param	array	$team
	 * 		The ID of the team this participant belongs to.
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public static function newWithAll($accountId, $name, $team) {
		$participant = new ChatParticipant();
		$participant->withAccountId($accountId)
			->withName($name)
			->withTeams(array($team));
		
		return $participant;
	}
	
	/**
	 * Creates a new ChatParticipant were only her ID is set.
	 * 
	 * @param	string	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 * 
	 * @deprecated	Use the "newWithKey" method instead.
	 */
	public static function newWithId($id) {
		$participant = new ChatParticipant();
		$participant->withId($id);
		
		return $participant;
	}
	
	/**
	 * Creates a new ChatParticipant were only her ID is set.
	 * 
	 * @param	string	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$accountId
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public static function newWithKey($chatId, $accountId) {
		$participant = new ChatParticipant();
		$participant->withChat($chatid)
			->withAccountId($accountId);
			
		return $participant;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function __construct() {
		$this->teams = array();
	}
	
	/**
	 * The URL of the participant's avatar.
	 * 
	 * See the ChatManager::findChatParticipantAvatarFromAccount for further details.
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getAvatar() {
		return $this->avatar;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getAccountId() {
		return $this->accountId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The owning Chat's ID.
	 */
	public function getChat() {
		return $this->chat;
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
	 * 		See the ChatManager::findChatParticipantNameFromAccount for further details.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		The ID of the first team this participant belongs to.
	 */
	public function getFirstTeam() {
		return $this->teams[0];
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		An array of integers that indicate the IDs of the teams this participant belongs to.
	 */
	public function getTeams() {
		return $this->teams;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$team
	 * 		The ID of the team this participant is checked against.
	 * 
	 * @return	boolean
	 * 		"true" if this participant belongs to the given team.
	 */
	public function isMemberOf($team) {
		foreach ($this->teams as $myTeam) {
			if ($myTeam->getId() === $team->getId()) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * {description}
	 * 
	 * @param	The URL of this participant's avatar.	$avatar
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		$this
	 */
	public function withAvatar($avatar) {
		if ($this->avatar === null) {
			$this->avatar = $avatar;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		$this
	 */
	public function withAccountId($id) {
		if ($this->accountId === null) {
			$this->accountId = $id;
		}
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$chat
	 * 		The ID of the Chat this participant is member of.
	 * 
	 * @return	ChatParticipant
	 * 		$this
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
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		$this
	 * 
	 * @deprecated	The ID field is no longer used. A composite primary key is used instead.
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
	 * @param	string	$name
	 * 		See method ChatManager::findChatParticipantNameFromAccount for further details.
	 * 
	 * @return	ChatParticipant
	 * 		$this
	 */
	public function withName($name) {
		if ($this->name === null) {
			$this->name = $name;
		}

		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$teams
	 * 		An array of integer that correspond to the IDs of the teams this participant belongs to.
	 * 
	 * @return	ChatParticipant
	 * 		$this
	 */
	public function withTeams(array $teams) {
		if (empty($this->teams)) {
			$this->teams = $teams;
		}
		
		return $this;
	}
}
//#section_end#
?>