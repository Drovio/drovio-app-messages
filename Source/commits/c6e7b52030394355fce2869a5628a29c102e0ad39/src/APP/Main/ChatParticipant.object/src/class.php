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
 * @version	3.0-1
 * @created	August 29, 2015, 11:57 (EEST)
 * @updated	September 2, 2015, 18:24 (EEST)
 */
class ChatParticipant {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $accountId;

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
	 * @type	string
	 */
	private $name;
	
	/**
	 * {description}
	 * 
	 * @type	ChatParticipantTeam[]
	 */
	private $teams;

	/**
	 * {description}
	 * 
	 * @param	{type}	$accountId
	 * 		{description}
	 * 
	 * @param	{type}	$name
	 * 		{description}
	 * 
	 * @param	{type}	$team
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public static function newWithAll($accountId, $name, ChatParticipantTeam $team) {
		$participant = new ChatParticipant();
		$participant->withAccountId($accountId)
			->withName($name)
			->addTeam($team);
		
		return $participant;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public static function newWithId($id) {
		$participant = new ChatParticipant();
		$participant->withId($id);
		
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
	 * {description}
	 * 
	 * @param	ChatParticipantTeam	$team
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public function addTeam($team) {
		$this->teams[] = $team;
		
		return $this;
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
	 * @return	string
	 * 		{description}
	 */
	public function getName() {
		return $this->name;
	}
	
	public function getFirstTeam() {
		return $this->teams[0];
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
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
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
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public function withChat(Chat $chat) {
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
	 * 		{description}
	 */
	public function withId($id) {
		$this->id = $id;
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$name
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public function withName($name) {
		$this->name = $name;
		
		return $this;
	}
}
//#section_end#
?>