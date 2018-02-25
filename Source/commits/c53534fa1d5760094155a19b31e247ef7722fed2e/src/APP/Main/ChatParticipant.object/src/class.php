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
 * @version	5.0-3
 * @created	August 29, 2015, 11:57 (EEST)
 * @updated	September 18, 2015, 22:52 (EEST)
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
	 * @type	integer
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
	 * @type	integer
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
	 * @param	integer	$team
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
	 * Creates a new ChatParticipant were only its id is set.
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
	 * @return	integer
	 * 		{description}
	 */
	public function getAccountId() {
		return $this->accountId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
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
	 * @return	integer
	 * 		The IDs of the teams this participant belongs to.
	 */
	public function getTeams() {
		return $this->teams;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$team
	 * 		{description}
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
	 * @param	integer	$chat
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
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
	 * @param	string	$name
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
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
	 * @param	integer	$teams
	 * 		The IDs of the teams this participant belongs to.
	 * 
	 * @return	ChatParticipant
	 * 		{description}
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