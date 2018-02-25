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
 * @version	3.0-3
 * @created	August 29, 2015, 11:23 (EEST)
 * @updated	September 7, 2015, 12:58 (EEST)
 */
class ChatManager {

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	const DATE_TIME_LOCAL_FORMAT = 'm/d/Y H:i:s';

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
		$nowString = $now->format(self::DATE_TIME_FORMAT);
		
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
			. '(NULL, ' . $authorId . ', ' . $chatId . ', "' . $content . '", NULL);';
		$query .= 'SELECT LAST_INSERT_ID() as id;';
		$dbc = $this->dbConnBuilder->getConnection();
		$resultSet = $dbc->execute($query);
		$result = $dbc->fetch($resultSet);
		$newMessageId = $result['id'];
		
		$createdAtQuery = 'SELECT created_at'
			. ' FROM ' . static::$DB_TABLE_MESSAGES
			. ' WHERE id = ' . $newMessageId;
		$resultSet = $dbc->execute($createdAtQuery);
		$result = $dbc->fetch($resultSet);
		$createdAt = $result['created_at'];
		
		$newMessage = ChatMessage::newWithAll($chatId, $authorId, $content);
		$newMessage->withId($newMessageId)
			->withCreatedAt($createdAt);
		
		$this->updateChatLastMessageId($chatId, $newMessageId);
		
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
	
	public function findChatMessages($chatId) {
		$query = 'SELECT id'
				. ', author_id'
				. ', chat_id'
				. ', DATE_FORMAT(created_at, "%m/%d/%Y %H:%i:%s") AS created_at'
				. ', content'
			. ' FROM ' . static::$DB_TABLE_MESSAGES
			. ' WHERE chat_id = ' . $chatId
			. ' ORDER BY created_at';
		$dbc = $this->dbConnBuilder->getConnection();
		$results = $dbc->execute($query, true);
		
		$messages = array();
		foreach ($results as $result) {
			$message = ChatMessage::newWithId($result['id'])
				->withAuthor($result['author_id'])
				->withChat($result['chat_id'])
				->withCreatedAt($result['created_at'])
				->withContent($result['content']);
			$messages[$message->getId()] = $message;
		}
		
		return $messages;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$accountId
	 * 		{description}
	 * 
	 * @param	integer	$chatId
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
	
	/**
	 * {description}
	 * 
	 * @param	integer	$accountId
	 * 		{description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function findChatParticipantNameFromAccount($accountId) {
		$profileInfo = account::info($accountId);
		
		return $profileInfo['accountTitle'];
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$limit
	 * 		{description}
	 * 
	 * @return	Chat[]
	 * 		{description}
	 */
	public function findLastChatsOfParticipant($participantId, $limit = 1) {
		$query = 'SELECT chats.id'
				. ', chats.created_at'
				. ', chats.last_message_id'
				. ', chats.owner_id'
				. ', chats.team_id'
				. ', DATE_FORMAT(messages.created_at, "%m/%d/%Y %H:%i:%s") AS "msg.created_at"'
				. ', messages.author_id AS "msg.author_id"'
				. ', messages.content AS "msg.content"'
			. ' FROM ' . static::$DB_TABLE_CHAT_PARTICIPANTS . ' participants'
				. ' INNER JOIN ' . static::$DB_TABLE_CHATS . ' chats'
					. ' ON (participants.chat_id = chats.id)'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGES . ' messages'
					. ' ON (chats.last_message_id = messages.id)'
			. ' WHERE participants.account_id = ' . $participantId
			. ' ORDER BY messages.created_at DESC'
			. ' LIMIT 0, ' . $limit;
		$dbc = $this->dbConnBuilder->getConnection();
		$results = $dbc->execute($query, true);
		
		$chats = array();
		foreach ($results as $result) {
			$chat = Chat::newWithId($result['id'])
				->withCreatedAt(\DateTime::createFromFormat(
					self::DATE_TIME_FORMAT,
					$result['created_at'],
					new \DateTimeZone('UTC')
				))
				->withLastMessageId($result['last_message_id'])
				->withOwner($result['owner_id'])
				->withTeam($result['team_id']);
			$lastMessage = ChatMessage::newWithId($chat->getLastMessageId())
				->withAuthor($result['msg.author_id'])
				->withChat($chat->getId())
				->withCreatedAt($result['msg.created_at'])
				->withContent($result['msg.content']);
				
			$chats[$chat->getId()] = array(
				'chat' => $chat,
				'lastMessage' => $lastMessage
			);
		}
		
		return $chats;
	}
	
	/**
	 * {description}
	 * 
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @return	Chat
	 * 		{description}
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
	
	/**
	 * {description}
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$lastMessageId
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function updateChatLastMessageId($chatId, $lastMessageId) {
		$query = 'UPDATE ' . static::$DB_TABLE_CHATS
			. ' SET last_message_id = ' . $lastMessageId
			. ' WHERE id = ' . $chatId;
		$this->dbConnBuilder->getConnection()->execute($query);
	}
}
//#section_end#
?>