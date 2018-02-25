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
importer::import("AEL", "Literals", "appLiteral");
application::import("Comm", "DatabaseConnectionBuilder");
application::import("Main", "Chat");
application::import("Main", "ChatParticipant");
application::import("Main", "ChatParticipantTeam");
application::import("Main", "NewChatRequest");
application::import("Main", "NewChatMessageRequest");
application::import("Main", "ChatMessage");
application::import("Main", "PendingMessage");
application::import("Main", "Cache");
application::import('Persist', 'IdGeneratorInterface');

use API\Platform\engine;
use API\Profile\account;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\Chat;
use APP\Main\ChatParticipant;
use APP\Main\ChatParticipantTeam;
use APP\Main\NewChatRequest;
use APP\Main\NewChatMessageRequest;
use APP\Main\ChatMessage;
use APP\Main\PendingMessage;
use APP\Main\Cache;
use AEL\Literals\appLiteral;
use APP\Persist\IdGeneratorInterface;

/**
 * Performs all the CRUD operations of the Messages application.
 * 
 * {description}
 * 
 * @version	8.0-2
 * @created	August 29, 2015, 11:23 (EEST)
 * @updated	September 20, 2015, 16:24 (EEST)
 */
class ChatManager {

	/**
	 * The format used when storing dates to the database.
	 * 
	 * @type	string
	 */
	const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
	
	/**
	 * The format used when displaying dates to the user.
	 * 
	 * @type	string
	 */
	const DATE_TIME_LOCAL_FORMAT = 'm/d/Y H:i:s';
	
	/**
	 * The format used when retrieving timestamps from the database.
	 * 
	 * @type	string
	 */
	private static $DB_DATE_FORMAT = "%m/%d/%Y %H:%i:%s";

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_CHAT_PARTICIPANTS = 'MSG_conversationParticipant';
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_CHATS = 'MSG_conversation';
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_MESSAGE_OWNER = 'MSG_messageOwner';
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_MESSAGES = 'MSG_message';
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private static $DB_TABLE_PENDING_MESSAGES = 'MSG_pendingMessage';
	
	/**
	 * {description}
	 * 
	 * @type	Cache
	 */
	private $chatParticipantNameCache;

	/**
	 * {description}
	 * 
	 * @type	DatabaseConnectionBuilder
	 */
	private $dbConnBuilder;
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $idGenerator;

	/**
	 * {description}
	 * 
	 * @param	DatabaseConnectionBuilder	$dbConnBuilder
	 * 		{description}
	 * 
	 * @param	{type}	$idGenerator
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(DatabaseConnectionBuilder $dbConnBuilder, IdGeneratorInterface $idGenerator) {
		$this->dbConnBuilder = $dbConnBuilder;
		$this->idGenerator = $idGenerator;
		$this->chatParticipantNameCache = new Cache();
	}
	
	/**
	 * A chat is active when the participant has opened it.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function activateChatForParticipant($chatId, $participantId) {
		$attributes = array(
			'chat_id' => $chatId,
			'account_id' => $participantId
		);
		$query = 'UPDATE ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' SET active = 1'
			. ' WHERE chat_id = "{chat_id}"'
				. ' AND account_id = {account_id}';	
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * {description}
	 * 
	 * @param	ChatMessage	$message
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function createMessageCopies(ChatMessage $message) {
		$chatId = $message->getChat();
		$participants = $this->findChatParticipants($chatId);
		
		$query = 'INSERT INTO ' . static::$DB_TABLE_MESSAGE_OWNER
			. ' (`owner_id`, `message_id`, `time_created`, `time_read`) VALUES'
			. ' ({owner_id}, "{message_id}", "{time_created}", NULL)';
			
		$messageId = $message->getId();
		$messageCreatedAt = $message->getCreatedAt();
		$attributes = array(
			'owner_id' => '',
			'time_created' => $messageCreatedAt,
			'message_id' => $messageId
		);
		
		foreach ($participants as $p) {
			$attributes['owner_id'] = $p->getAccountId();
			$this->executeQuery($query, $attributes);
		}
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
		$nowUnixTimestamp = time();
//		$nowString = $now->format(self::DATE_TIME_FORMAT);
		$ownerId = $request->getOwnerId();
		$teamId = $request->getTeamId();
		$id = $this->idGenerator->generate(array($teamId, $ownerId));
		
		$attributes = array(
			'id' => $id,
			'owner_id' => $ownerId,
			'team_id' => $teamId,
			'created_at' => $nowUnixTimestamp
		);
		$query = 'INSERT INTO ' . static::$DB_TABLE_CHATS
			. ' (`id`, `owner_id`, `team_id`, `created_at`, `last_message_id`) VALUES'
			. ' ("{id}", {owner_id}, {team_id}, {created_at}, NULL)';
		$this->executeQuery($query, $attributes);
		
		$chat = Chat::newWithAll(
			$request->getOwnerId(),
			$request->getRecipientsIds(),
			$request->getTeamId(),
			$now
		);
		$chat->withId($id);
		
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
		$now = new \DateTime('now', new \DateTimeZone('UTC'));
		$nowUnixTimestamp = time();
//		$nowString = $now->format(self::DATE_TIME_FORMAT);
		$chatId = $request->getChatId();
		$authorId = $request->getAuthorId();
		$content = $request->getContent();
		$id = $this->idGenerator->generate(array($chatId, $authorId));
		
		$attributes = array(
			'id' => $id,
			'content' => $content,
			'created_at' => $nowUnixTimestamp,
			'author_id' => $authorId,
			'chat_id' => $chatId
		);
		$query = 'INSERT INTO ' . static::$DB_TABLE_MESSAGES
			. ' (`id`, `content`, `created_at`, `author_id`, `chat_id`) VALUES '
			. '("{id}", "{content}", "{created_at}", {author_id}, "{chat_id}")';
		$this->executeQuery($query, $attributes);
		
		$newMessage = ChatMessage::newWithAll($chatId, $authorId, $content);
		$newMessage->withId($id)
			->withCreatedAt($nowString);
		
		$this->updateChatLastMessageId($chatId, $id);
		
		return $newMessage;
	}
	
	/**
	 * A message is pending when it can be retrieved at the next "Message Polling Cycle" (MPC).
	 * 
	 * The MPC is the mechanism used by the client in order to ask at specific time intervals for any new messages in the context of the currently active chat.
	 * 
	 * So, creating a new pending message is like saying to the MPC that this message should be displayed to the user at the next cycle.
	 * 
	 * It should be noted that no new message is created. Just a reference to an already created message.
	 * 
	 * See the "activateChatForParticipant" method for the definition of an active chat.
	 * 
	 * @param	integer	$messageId
	 * 		{description}
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function createNewPendingMessage($messageId, $chatId) {
		$participants = $this->findChatParticipants($chatId);
	
		$query = 'INSERT INTO ' . static::$DB_TABLE_PENDING_MESSAGES
			. ' (`participant_id`, `message_id`) VALUES'
			. ' ({participant_id}, "{message_id}")';
		$attributes = array('message_id' => $messageId);
		
		foreach ($participants as $participant) {
			$attributes['participant_id'] = $participant->getAccountId();
			$this->executeQuery($query, $attributes);
		}
	}
	
	/**
	 * See the "activateChatForParticipant" for the definition of an active chat.
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function deactivateAllChatsOfParticipant($participantId) {
		$attributes = array('account_id' => $participantId);
		$query = 'UPDATE ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' SET active = 0'
			. ' WHERE account_id = {account_id}';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * See the "activateChatForParticipant" for the definition of an active chat.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function deactivateChatForParticipant($chatId, $participantId) {
		$attributes = array(
			'chat_id' => $chatId,
			'account_id' => $participantId
		);
		$query = 'UPDATE ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' SET active = 0'
			. ' WHERE chat_id = "{chat_id}"'
				. ' AND account_id = {account_id}';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * Important: Deleting a chat causes the deletion of all its messages, too.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function deleteChat($chatId) {
		$attributes = array('id' => $chatId);
		$query = 'DELETE FROM ' . static::$DB_TABLE_CHATS
			. ' WHERE id = "{id}"';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * Marks a chat as "deleted" for a participant.
	 * 
	 * This method differs from the "deleteChat" method in that it just marks the chat as "deleted" for a participant instead of deleting it.
	 * 
	 * Furthermore, it deletes all of this chat messages' copies that have been created for this participant.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function deleteChatForParticipant($chatId, $participantId) {
		$this->deleteChatMessageCopiesForParticipant($chatId, $participantId);
		$this->setChatDeletedForParticipant($chatId, $participantId, 1);
	}
	
	/**
	 * See the "createNewPendingMessage" for the definition of a pending message.
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function deletePendingMessage($id) {
		$attributes = array('id' => $id);
		$query = 'DELETE FROM ' . static::$DB_TABLE_PENDING_MESSAGES
			. ' WHERE id = {id}';
		$this->executeQuery($query, $attributes);
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
		$attributes = array('id' => $id);
		$query = 'SELECT *'
			. ' FROM ' . static::$DB_TABLE_CHATS
			. ' WHERE id = "{id}"';
		$resultSet = $this->executeQuery($query, $attributes);
		$result = $this->dbConnBuilder->getConnection()->fetch($resultSet);
		if (empty($result)) {
			throw new \InvalidArgumentException('Unknown chat: ' . $id);
		}
		
		$chat = Chat::newWithId($result['id'])
			->withOwner($result['owner_id'])
			->withTeam($result['team_id']);
		
		return $chat;
	}
	
	/**
	 * The messages are returned in an ascending order based on their creation time.
	 * 
	 * The "myUserAccountId" parameter is used for the normalization of the author's name. See the "getNormalizedMessageAuthorName" for more information.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$myUserAccountId
	 * 		{description}
	 * 
	 * @return	ChatMessage[]
	 * 		{description}
	 */
	public function findChatMessages($chatId, $myUserAccountId) {
		$attributes = array('chat_id' => $chatId);
		$query = 'SELECT id'
				. ', author_id'
				. ', chat_id'
//				. ', DATE_FORMAT(created_at, "%m/%d/%Y %H:%i:%s") AS created_at_string'
				. ', FROM_UNIXTIME(created_at, "' . static::$DB_DATE_FORMAT . '") AS created_at_string'
				. ', content'
			. ' FROM ' . static::$DB_TABLE_MESSAGES
			. ' WHERE chat_id = "{chat_id}"'
			. ' ORDER BY created_at ASC';
		$resultSet = $this->executeQuery($query, $attributes);
		$results = $this->dbConnBuilder->getConnection()->fetchAll($resultSet);
		
		return $this->setUpMessagesDbResult($results, $myUserAccountId);
	}
	
	/**
	 * The "myUserAccountId" parameter is used for the normalization of the author's name. See the "getNormalizedMessageAuthorName" for more information.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$myUserAccountId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function findChatMessagesCopies($chatId, $myUserAccountId) {
		$attributes = array(
			'chat_id' => $chatId,
			'owner_id' => $myUserAccountId
		);
		$query = 'SELECT m.id AS "id"'
				. ', author_id'
				. ', chat_id'
//				. ', DATE_FORMAT(created_at, "%m/%d/%Y %H:%i:%s") AS created_at_string'
				. ', FROM_UNIXTIME(created_at, "' . static::$DB_DATE_FORMAT . '") AS created_at_string'
				. ', content'
			. ' FROM ' . static::$DB_TABLE_MESSAGES . ' m'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGE_OWNER . ' mo'
					. ' ON (m.id = mo.message_id)'
			. ' WHERE chat_id = "{chat_id}"'
				. ' AND mo.owner_id = {owner_id}'
			. ' ORDER BY created_at ASC';
		$resultSet = $this->executeQuery($query, $attributes);
		$results = $this->dbConnBuilder->getConnection()->fetchAll($resultSet);
		
		return $this->setUpMessagesDbResult($results, $myUserAccountId);
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
		$attributes = array(
			'account_id' => $accountId,
			'chat_id' => $chatId
		);
		$query = 'SELECT *'
			. ' FROM ' . static::$DB_TABLE_CHAT_PARTICIPANTS . ' participant'
				. ' INNER JOIN ' . static::$DB_TABLE_CHATS . ' chat'
					. ' ON (participant.chat_id = chat.id) '
			. ' WHERE account_id = {account_id}'
				. ' AND chat_id = "{chat_id}"';
		$resultSet = $this->executeQuery($query, $attributes);
		$result = $this->dbConnBuilder->getConnection()->fetch($resultSet);
		if (empty($result)) {
			throw new \InvalidArgumentException('Unknown chat participant "' . $accountId 
				. '" for chat "' . $chatId . '".');
		}
		
		$name = $this->findChatParticipantNameFromAccount($accountId);
		
		$participant = ChatParticipant::newWithKey($result['chat_id'], $result['account_id'])
			->withTeams(array($result['team_id']))
			->withName($name);
			
		return $participant;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$deleted
	 * 		Valid values: 0, 1, -1.
	 * 		
	 * 		The values are interpreted as follows:
	 * 		 - 0 → A participant must not have deleted the chat.
	 * 		 - 1 → A participant must have deleted the chat.
	 * 		 - -1 → Not interested whether a participant has deleted the chat or not. In other words, all chat participants should be returned.
	 * 
	 * @return	ChatParticipant[]
	 * 		{description}
	 */
	public function findChatParticipants($chatId, $deleted = -1) {
		$attributes = array('chat_id' => $chatId);
		$query = 'SELECT *'
			. ' FROM ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' WHERE chat_id = "' . $chatId . '"';
		if ($deleted !== -1) {
			$attributes['deleted'] = $deleted;
			$query .= ' AND deleted = {deleted}';
		}
		$resultSet = $this->executeQuery($query, $attributes);
		$results = $this->dbConnBuilder->getConnection()->fetchAll($resultSet);
		
		$participants = array();
		foreach ($results as $result) {
			$name = $this->findChatParticipantNameFromAccount($result['account_id']);
		
			$participant = ChatParticipant::newWithKey($chatId, $result['account_id'])
				->withName($name);
				
			$participants[] = $participant;
		}
			
		return $participants;
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
		if ($this->chatParticipantNameCache->isPresent($accountId)) {
			return $this->chatParticipantNameCache->get($accountId);
		}
		
		$profileInfo = account::info($accountId);
		$name = $profileInfo['accountTitle'];
		$this->chatParticipantNameCache->add($accountId, $name);
		
		return $name;
	}
	
	/**
	 * Returns the last "limit" chats of the participant.
	 * 
	 * For example, if "limit" equals "1", then only the last chat is returned.
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$limit
	 * 		{description}
	 * 
	 * @return	Chat[]
	 * 		An array of the form:
	 * 		 array(
	 * 		   <chat ID>:integer => array(
	 * 		     'chat' => Chat,
	 * 		     'lastMessage' => ChatMessage
	 * 		   )
	 * 		 )
	 * 
	 * @deprecated	Use the "findLastChatsOfParticipantForTeam" method instead.
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
				. ', participants.updated'
				. ', participants.active'
			. ' FROM ' . static::$DB_TABLE_CHAT_PARTICIPANTS . ' participants'
				. ' INNER JOIN ' . static::$DB_TABLE_CHATS . ' chats'
					. ' ON (participants.chat_id = chats.id)'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGES . ' messages'
					. ' ON (chats.last_message_id = messages.id)'
			. ' WHERE participants.account_id = ' . $participantId
			. ' ORDER BY participants.updated DESC, messages.created_at DESC'
			. ' LIMIT 0, ' . $limit;
		$results = $this->dbConnBuilder->getConnection()->execute($query, true);
		
		$chats = array();
		foreach ($results as $result) {
			$chat = Chat::newWithId($result['id'])
				->withActive($result['active'])
				->withCreatedAt(\DateTime::createFromFormat(
					self::DATE_TIME_FORMAT,
					$result['created_at'],
					new \DateTimeZone('UTC')
				))
				->withLastMessageId($result['last_message_id'])
				->withOwner($result['owner_id'])
				->withTeam($result['team_id'])
				->withUpdated($result['updated']);
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
	 * Returns the last "limit" chats of the participant that belong to a particular team.
	 * 
	 * For example, if "limit" equals "1", then only the last chat is returned.
	 * 
	 * @param	integer	$teamId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$limit
	 * 		Valid values:
	 * 		 - -1 → No limit. All chats are returned.
	 * 		 - >= 0 → This amount of chats when sorted in descending order based on the creation time.
	 * 
	 * @return	array
	 * 		An array of the form:
	 * 		 array(
	 * 		   <chat ID>:integer => array(
	 * 		     'chat' => Chat,
	 * 		     'lastMessage' => ChatMessage
	 * 		   )
	 * 		 )
	 */
	public function findLastChatsOfParticipantForTeam($teamId, $participantId, $limit = -1) {
		$attributes = array(
			'participant_id' => $participantId,
			'team_id' => $teamId
		);
		$query = 'SELECT chats.id'
				. ', FROM_UNIXTIME(chats.created_at, "' . static::$DB_DATE_FORMAT . '") AS "created_at"'
				. ', chats.last_message_id'
				. ', chats.owner_id'
				. ', chats.team_id'
				. ', DATE_FORMAT(messages.created_at, "%m/%d/%Y %H:%i:%s") AS "msg.created_at"'
				. ', FROM_UNIXTIME(messages.created_at, "' . static::$DB_DATE_FORMAT . '") AS "msg.created_at"'
				. ', messages.author_id AS "msg.author_id"'
				. ', messages.content AS "msg.content"'
				. ', participants.updated'
				. ', participants.active'
			. ' FROM ' . static::$DB_TABLE_CHAT_PARTICIPANTS . ' participants'
				. ' INNER JOIN ' . static::$DB_TABLE_CHATS . ' chats'
					. ' ON (participants.chat_id = chats.id)'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGES . ' messages'
					. ' ON (chats.last_message_id = messages.id)'
			. ' WHERE participants.account_id = {participant_id}'
				. ' AND chats.team_id = {team_id}'
				. ' AND participants.deleted = 0'
			. ' ORDER BY participants.updated DESC, messages.created_at DESC';
		if ($limit !== -1) {
			$attributes['limit'] = $limit;
			$query .= ' LIMIT 0, {limit}';
		}
		$resultSet = $this->executeQuery($query, $attributes);
		$results = $this->dbConnBuilder->getConnection()->fetchAll($resultSet);
		
		$chats = array();
		foreach ($results as $result) {
			$chat = Chat::newWithId($result['id'])
				->withActive($result['active'])
				->withCreatedAt(\DateTime::createFromFormat(self::DATE_TIME_LOCAL_FORMAT, $result['created_at']))
				->withLastMessageId($result['last_message_id'])
				->withOwner($result['owner_id'])
				->withTeam($result['team_id'])
				->withUpdated($result['updated']);
			$participants = $this->findChatParticipants($chat->getId());			
			$chat->withParticipants($participants);				
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
	 * See the "createNewPendingMessage" for the definition of a pending message.
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function deletePendingMessagesOfChatParticipant($participantId, $chatId) {
		$attributes = array(
			'participant_id' => $participantId,
			'chat_id' => $chatId
		);
		$query = 'DELETE pm'
			. ' FROM ' . static::$DB_TABLE_PENDING_MESSAGES . ' pm'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGES . ' m'
					. ' ON (pm.message_id = m.id)'
			. ' WHERE pm.participant_id = {participant_id}'
				. ' AND m.chat_id = "{chat_id}"';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * See the "createNewPendingMessage" for the definition of a pending message.
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @return	PendingMessage[]
	 * 		A PendingMessage array where the keys are the IDs of the messages.
	 */
	public function findPendingMessagesOfChatParticipant($participantId, $chatId) {
		$attributes = array(
			'participant_id' => $participantId,
			'chat_id' => $chatId
		);
		$query = 'SELECT pm.message_id'
				. ', pm.participant_id'
				. ', m.author_id'
				. ', m.content'
//				. ', DATE_FORMAT(m.created_at, "' . static::$DB_DATE_FORMAT . '") AS "created_at"'
				. ', FROM_UNIXTIME(m.created_at, "' . static::$DB_DATE_FORMAT . '") AS "created_at"'
			. ' FROM ' . static::$DB_TABLE_PENDING_MESSAGES . ' pm'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGES . ' m'
					. ' ON (pm.message_id = m.id)'
			. ' WHERE pm.participant_id = {participant_id}'
				. ' AND m.chat_id = "{chat_id}"'
			. ' ORDER BY m.created_at ASC';

		$resultSet = $this->executeQuery($query, $attributes);
		$results = $this->dbConnBuilder->getConnection()->fetchAll($resultSet);
		
		$messages = array();
		foreach ($results as $result) {
			$authorId = $result['author_id'];
			$authorName = $this->getNormalizedMessageAuthorName($authorId, $participantId);
		
			$message = PendingMessage::newWithAll($result['message_id'], $result['participant_id'])
				->withAuthor($authorId)
				->withAuthorName($authorName)
				->withContent($result['content'])
				->withCreatedAt($result['created_at'])
				->withMine($this->isMyMessage($authorId, $participantId));
			$messages[] = $message;
		}		
		
		return $messages;
	}
	
	/**
	 * See the "activateChatForParticipant" for the definition of an active chat.
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$active
	 * 		Valid values:
	 * 		 - 0 → marks the chat as inactive.
	 * 		 - 1 → marks the chat as active.
	 * 
	 * @return	void
	 */
	public function setChatActiveForParticipant($chatId, $participantId, $active) {
		$attributes = array(
			'active' => $active,
			'chat_id' => $chatId,
			'account_id' => $participantId
		);
		$query = 'UPDATE ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' SET active = {active}'
			. ' WHERE chat_id = "{chat_id}"'
				. ' AND account_id = {account_id}';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * See the "deleteChatForParticipant" for the definition of the "deleted for a participant chat".
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function undeleteChat($chatId) {
		$attributes = array('chat_id' => $chatId);
		$query = 'UPDATE ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' SET deleted = 0'
			. ' WHERE chat_id = "{chat_id}"';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function deleteChatMessageCopiesForParticipant($chatId, $participantId) {
		$attributes = array(
			'chat_id' => $chatId,
			'owner_id' => $participantId
		);
		$query = 'DELETE mo'
			. ' FROM ' . static::$DB_TABLE_MESSAGE_OWNER . ' mo'
				. ' INNER JOIN ' . static::$DB_TABLE_MESSAGES . ' m'
				. ' ON (mo.message_id = m.id)'
			. ' WHERE m.chat_id = "{chat_id}"'
				. ' AND mo.owner_id = {owner_id}';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$query
	 * 		{description}
	 * 
	 * @param	{type}	$attributes
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function executeQuery($query, $attributes = array()) {
		$dbc = $this->dbConnBuilder->getConnection();
		$result = $dbc->execute($query, $attributes);
		if ($result === false) {
			$m = 'Failed Query: ' . $query . PHP_EOL 
				. 'Attributes: ' . print_r($attributes) . PHP_EOL
				. 'DBMS Message: ' . $dbc->getError();
			throw new \Exception($m);
		}
		
		return $result;
	}
	
	/**
	 * The "normalized" version of a message author's name is created via the following logic:
	 *  - If the author is the user currently logged in, then the literal "util_me" of the "chat" scope is returned.
	 *  - In any other case, the author's name is the profile name of the user with ID equal to "authorId".
	 * 
	 * This value is useful for places where the author of a message is displayed and that author is the current user. So, for example, if the current user's name is "John Doe", he would see a label such as "Me" next to his messages. That is more user-friendly than seeing his own name.
	 * 
	 * @param	integer	$authorId
	 * 		{description}
	 * 
	 * @param	integer	$myUserAccountId
	 * 		{description}
	 * 
	 * @return	string
	 * 		The literal "chat/util_me" or the profile name of the user with id "authorId".
	 */
	private function getNormalizedMessageAuthorName($authorId, $myUserAccountId) {
		return $this->isMyMessage($authorId, $myUserAccountId)
			? appLiteral::get('chat', 'util_me', array(), false)
			: $this->findChatParticipantNameFromAccount($authorId);
	}
	
	/**
	 * Checks if the author of a message is the current user.
	 * 
	 * The check was refactored into a method for maintenance reasons. Since this is a check performed in multiple places, should the check requires change it will have to be changed in only one place.
	 * 
	 * @param	integer	$messageAuthorId
	 * 		{description}
	 * 
	 * @param	integer	$myUserAccountId
	 * 		{description}
	 * 
	 * @return	boolean
	 * 		Returns "true" if the two IDs are equal.
	 */
	private function isMyMessage($messageAuthorId, $myUserAccountId) {
		return $messageAuthorId === $myUserAccountId;
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
		$ownerId = $chat->getOwner();
		
		$query = 'INSERT INTO ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' (`chat_id`, `account_id`, `updated`, `active`, `deleted`) VALUES '
			. ' ("{chat_id}", {account_id}, 1, {active}, 0)';
		$attributes = array('chat_id' => $chatId);
		
		foreach ($chat->getParticipants() as $participantId) {
			$attributes['account_id'] = $participantId;
			$attributes['active'] = $participantId === $ownerId ? 1 : 0;
			
			$this->executeQuery($query, $attributes);
		}
	}
	
	/**
	 * See the "deleteChatForParticipant" for the definition of a "deleted for a participant chat".
	 * 
	 * @param	integer	$chatId
	 * 		{description}
	 * 
	 * @param	integer	$participantId
	 * 		{description}
	 * 
	 * @param	integer	$deleted
	 * 		Valid values:
	 * 		 - 0 → marks the chat as not deleted.
	 * 		 - 1 → marks the chat as deleted.
	 * 
	 * @return	void
	 */
	private function setChatDeletedForParticipant($chatId, $participantId, $deleted) {
		$attributes = array(
			'deleted' => $deleted,
			'chat_id' => $chatId,
			'account_id' => $participantId
		);
		$query = 'UPDATE ' . static::$DB_TABLE_CHAT_PARTICIPANTS
			. ' SET deleted = {deleted}'
			. ' WHERE chat_id = "{chat_id}"'
				. ' AND account_id = {account_id}';
		$this->executeQuery($query, $attributes);
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$results
	 * 		An array of the form:
	 * 		 array(
	 * 		   'author_id' => integer,
	 * 		   'id' => integer,
	 * 		   'chat_id' => integer,
	 * 		   'created_at' => string,
	 * 		   'content' => string
	 * 		 )
	 * 
	 * @param	integer	$myUserAccountId
	 * 		{description}
	 * 
	 * @return	ChatMessage[]
	 * 		{description}
	 */
	private function setUpMessagesDbResult($results, $myUserAccountId) {
		$messages = array();
		foreach ($results as $result) {
			$authorId = $result['author_id'];
			$authorName = $this->getNormalizedMessageAuthorName($authorId, $myUserAccountId);
			
			$message = ChatMessage::newWithId($result['id'])
				->withAuthor($authorId)
				->withAuthorName($authorName)
				->withChat($result['chat_id'])
				->withCreatedAt($result['created_at_string'])
				->withContent($result['content'])
				->withMine($this->isMyMessage($authorId, $myUserAccountId));
			$messages[] = $message;
		}
		
		return $messages;
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
		$attributes = array(
			'last_message_id' => $lastMessageId,
			'id' => $chatId
		);
		$query = 'UPDATE ' . static::$DB_TABLE_CHATS
			. ' SET last_message_id = "{last_message_id}"'
			. ' WHERE id = "{id}"';
		$this->executeQuery($query, $attributes);
	}
}
//#section_end#
?>