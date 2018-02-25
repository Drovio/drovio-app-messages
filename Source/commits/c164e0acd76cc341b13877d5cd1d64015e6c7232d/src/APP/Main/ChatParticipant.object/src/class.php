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
 * {title}
 * 
 * {description}
 * 
 * @version	1.0-1
 * @created	August 29, 2015, 11:57 (EEST)
 * @updated	August 29, 2015, 12:09 (EEST)
 */
class ChatParticipant {

	/**
	 * {description}
	 * 
	 * @type	Chat
	 */
	private $chat;

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $id;
	
	/**
	 * {description}
	 * 
	 * @type	ChatParticipantTeam[]
	 */
	private $teams;

	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @param	ChatParticipantTeam	$team
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct($id, ChatParticipantTeam $team) {
		$this->id = $id;
		
		$this->teams = array();
		$this->teams[$team->getId()] = $team;
	}
	
	/**
	 * {description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function getChat() {
		return $this->chat;
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
	 * @return	ChatParticipantTeam[]
	 * 		{description}
	 */
	public function getTeams() {
		return $this->teams;
	}
	
	/**
	 * {description}
	 * 
	 * @param	ChatParticipantTeam	$team
	 * 		{description}
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	public function isMemberOf(ChatParticipantTeam $team) {
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
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function setChat(Chat $chat) {
		$this->chat = $chat;
	}
}
//#section_end#
?>