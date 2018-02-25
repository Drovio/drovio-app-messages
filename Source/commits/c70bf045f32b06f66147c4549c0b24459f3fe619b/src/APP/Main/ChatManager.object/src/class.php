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
application::import("Main", "NewChatMessageRequest");
application::import("Main", "ChatMessage");

use API\Platform\engine;
use API\Profile\account;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\Chat;
use APP\Main\ChatParticipant;
use APP\Main\ChatParticipantTeam;
use APP\Main\NewChatRequest;
use APP\Main\NewChatMessageRequest;
use APP\Main\ChatMessage;

/**
 * Performs CRUD operations about chats.
 * 
 * {description}
 * 
 * @version	2.0-1
 * @created	August 29, 2015, 11:23 (EEST)
 * @updated	September 2, 2015, 19:45 (EEST)
 */
class ChatManager {

	/**
	 * {description}
	 * 
	 * @type	engine
	 */
	private static $DB_TABLE_CHAT_PARTICIPANTS = 'chat_participants';
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_CHATS = 'chats';
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_MESSAGES = 'chat_messages';

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
	 * @param	NewChatRequest	$request
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function createNewChat(NewChatRequest $request) {		
		$now = new \DateTime('now', new \DateTimeZone('UTC'));
		$nowString = $now->format('Y-m-d H:i:s');
		
		$query = 'INSERT INTO ' . static::$DB_TABLE_CHATS . ' VALUES'
			. ' ('
				. 'NULL, '
				. $request->getOwnerId() . ', '
				. $request->getTeamId() . ', '
				. '"' . $nowString . '", '
				. 'NULL'
			. ');';
		$query .= 'SELECT LAST_INSERT_ID() as id;';
		$dbc = $this->dbConnBuilder->getConnection();
		$resultSet = $dbc->execute($query);
		$result = $dbc->fetch($resultSet);
		$chatId = $result['id'];
		
		$chat = Chat::newWithAll(
			$request->getOwnerId(),
			$request->getRecipientsIds(),
			$request->getTeamId(),
			$now
		);
		$chat->withId($chatId);
		
		$this->saveParticipantsOfNewChat($chat);
		
		return $chat;
	}
	
	/**
	 * {description}
	 * 
	 * @param	NewChatMessageRequest	$request
	 * 		{description}
	 * 
	 * @return	ChatMessage
	 * 		{description}
	 */
	public function createNewMessage(NewChatMessageRequest $request) {
		$chatId = $request->getChatId();
		$authorId = $request->getAuthorId();
		$content = $request->getContent();
		
		$query = 'INSERT INTO ' . static::$DB_TABLE_MESSAGES . ' VALUES '
			. '(NULL, ' . $authorId . ', ' . $chatId . ', "' . $content . '");';
		$query .= 'SELECT LAST_INSERT_ID() as id;';
		$dbc = $this->dbConnBuilder->getConnection();
		$resultSet = $dbc->execute($query);
		$result = $dbc->fetch($resultSet);
		$newMessageId = $result['id'];
		
		$newMessage = ChatMessage::newWithAll($chatId, $authorId, $content);
		$newMessage->withId($newMessageId);
		
		return $newMessage;
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
	public function findChat($id) {
		$dbc = $this->dbConnBuilder->getConnection();
		$query = 'SELECT *'
			. ' FROM ' . static::$DB_TABLE_CHATS
			. ' WHERE id = ' . $id;
		$resultSet = $dbc->execute($query);
		$result = $dbc->fetch($resultSet);
		if (empty($result)) {
			throw new \InvalidArgumentException('Unknown chat: ' . $id);
		}
		
		$chat = Chat::newWithId($result['id'])
			->withOwner($result['owner_id'])
			->withTeam($result['team_id']);
		
		return $chat;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$accountId
	 * 		{description}
	 * 
	 * @param	{type}	$chatId
	 * 		{description}
	 * 
	 * @return	ChatParticipant
	 * 		{description}
	 */
	public function findChatParticipant($accountId, $chatId) {
		$dbc = $this->dbConnBuilder->getConnection();
		$query = 'SELECT *'
			. ' FROM ' . static::$DB_TABLE_CHAT_PARTICIPANTS . ' participant'
				. ' INNER JOIN ' . static::$DB_TABLE_CHATS . ' chat'
					. ' ON (participant.chat_id = chat.id) '
			. ' WHERE account_id = ' . $accountId
				. ' AND chat_id = ' . $chatId;
		$resultSet = $dbc->execute($query);
		$result = $dbc->fetch($resultSet);
		if (empty($result)) {
			throw new \InvalidArgumentException('Unknown chat participant "' . $accountId . '" for chat "' . $chatId . '".');
		}
		
		$name = $this->findChatParticipantNameFromAccount($accountId);
		
		$participant = ChatParticipant::newWithId($result['id'])
			->withAccountId($accountId)
			->withChat($chatId)
			->withTeams(array($result['team_id']))
			->withName($name);
			
		return $participant;
	}
	
	public function findParticipantChat($participantId) {
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$accountId
	 * 		{description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	private function findChatParticipantNameFromAccount($accountId) {
		$profileInfo = account::info($accountId);
		
		return $profileInfo['accountTitle'];
	}
	
	/**
	 * {description}
	 * 
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function saveParticipantsOfNewChat(Chat $chat) {
		$chatId = $chat->getId();
		$queryDataFormat = "(NULL, %d, %d)";
		
		$queryDataArr = array();
		foreach ($chat->getParticipants() as $rId) {
			$queryData[] = sprintf($queryDataFormat, $chatId, $rId);
		}
		$queryData = implode(', ', $queryData);
		
		$query = 'INSERT INTO ' . static::$DB_TABLE_CHAT_PARTICIPANTS . ' VALUES ' . $queryData;
		$this->dbConnBuilder->getConnection()->execute($query);
	}
}
//#section_end#
?>