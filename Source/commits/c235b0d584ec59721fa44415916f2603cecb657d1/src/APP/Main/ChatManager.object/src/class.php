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

importer::import("API", "Platform", "engine");
importer::import("API", "Profile", "account");
application::import("Comm", "DatabaseConnectionBuilder");
application::import("Main", "Chat");
application::import("Main", "ChatParticipant");
application::import("Main", "ChatParticipantTeam");
application::import("Main", "NewChatRequest");

use API\Platform\engine;
use API\Profile\account;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\Chat;
use APP\Main\ChatParticipant;
use APP\Main\ChatParticipantTeam;
use APP\Main\NewChatRequest;

/**
 * Performs CRUD operations about chats.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	August 29, 2015, 11:23 (EEST)
 * @updated	August 29, 2015, 11:23 (EEST)
 */
class ChatManager {

	private static $DB_TABLE_CHATS = 'chats';

	/**
	 * {description}
	 * 
	 * @type	DatabaseConnectionBuilder
	 */
	private $dbConnBuilder;

	/**
	 * {description}
	 * 
	 * @param	DatabaseConnectionBuilder	$dbConnBuilder
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(DatabaseConnectionBuilder $dbConnBuilder) {
		$this->dbConnBuilder = $dbConnBuilder;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function createNewChat(NewChatRequest $request) {		
		try {
			$team = new ChatParticipantTeam($request->getTeamId());
			$owner = $this->buildChatParticipant($request->getOwnerId(), $team);
		
			$recipients = array();
			foreach ($request->getRecipientsIds() as $recipientId) {
				$recipients[] = $this->buildChatParticipant($recipientId, $team);
			}
		
			$chat = new Chat($owner, $recipients, $team);
			$chat = $this->saveNewChat($chat);
		} catch (\IllegalArguentException $ex) {
		}
		
		return $chat;
	}
	
	private function buildChatParticipant($participantId, ChatParticipantTeam $team) {
		$profileInfo = account::info($participantId);
	
		return new ChatParticipant($participantId, $profileInfo['accountTitle'], $team);
	}
	
	private function saveNewChat(Chat $chat) {
		$owner = $chat->getOwner();
		$team = $chat->getTeam();
	
		$query = 'INSERT INTO ' . static::$DB_TABLE_CHATS . ' VALUES'
			. ' (NULL, ' . $owner->getId() . ', ' . $team->getId() . ');';
		$query .= 'SELECT LAST_INSERT_ID() as id;';
		$dbc = $this->dbConnBuilder->getConnection();
		$resultSet = $dbc->execute($query);
		
		$result = $dbc->fetch($resultSet);
		$chatId = $result['id'];
		$chat->initId($chatId);
		
		return $chat;
	}
}
//#section_end#
?>