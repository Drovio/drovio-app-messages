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

importer::import('UI', 'Forms', 'Form');
importer::import('UI', 'Html', 'HTML');
importer::import("AEL", "Literals", "appLiteral");
importer::import('ESS', 'Prototype', 'AppActionFactory');
application::import('Main', 'ChatManager');
application::import('Main', 'GetMessagesFormBuilder');

use UI\Forms\Form;
use UI\Html\HTML;
use APP\Main\ChatManager;
use AEL\Literals\appLiteral;
use APP\Main\GetMessagesFormBuilder;
use ESS\Prototype\AppActionFactory;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	1.0-1
 * @created	September 7, 2015, 13:30 (EEST)
 * @updated	September 7, 2015, 16:27 (EEST)
 */
class UserChatListFormBuilder {

	private $actionFactory;

	/**
	 * {description}
	 * 
	 * @type	ChatManager
	 */
	private $chatManager;
	
	/**
	 * {description}
	 * 
	 * @type	DOMElement
	 */
	private $container;

	/**
	 * {description}
	 * 
	 * @type	Form
	 */
	private $form;
	
	/**
	 * {description}
	 * 
	 * @type	DOMElement
	 */
	private $list;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $userAccountId;
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$chatManager
	 * 		{description}
	 * 
	 * @param	{type}	$userAccountId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(ChatManager $chatManager, $userAccountId, AppActionFactory $actionFactory) {
		$this->chatManager = $chatManager;
		$this->userAccountId = $userAccountId;
		$this->actionFactory = $actionFactory;
		$this->items = array();
	}
	
	/**
	 * {description}
	 * 
	 * @return	UserChatListFormBuilder
	 * 		{description}
	 */
	public function buildContainer() {
		$this->container = HTML::div('', 'diomsg-user-chat-list-container');
		HTML::append($this->container, $this->list);
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	Form	$form
	 * 		{description}
	 * 
	 * @return	UserChatListFormBuilder
	 * 		{description}
	 */
	public function buildForm(Form $form) {
		$this->form = $form;
		
		$this->form->build()
			->engageApp($this->getActionView());
			
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$data
	 * 		The format of this array is:
	 * 		
	 * 		    array(
	 * 		      'chat' => Chat,
	 * 		      'lastMessage' => ChatMessage
	 * 		    )
	 * 
	 * @return	UserChatListFormBuilder
	 * 		{description}
	 */
	public function buildItem($chatListItem) {
		$item = $this->buildItemWith($chatListItem);
		HTML::append($this->list, $item);
		
		return $this;
	}
	
	public function buildItemWith($chatListItem) {
		$chat = $chatListItem['chat'];
		$lastMessage = $chatListItem['lastMessage'];
		
		$chatId = $chat->getId();
		$deleteChatButton = $this->buildDeleteChatButton($chatId);
		$buttonContainer = $this->buildButtonContainer();
		$participantsElement = $this->buildChatParticipantsElement($chat);
		$lastMessageAuthorElement = $this->buildChatLastMessageAuthor($lastMessage->getAuthor());
		$lastMessageContentElement = $this->buildChatLastMessageContent($lastMessage->getContent());
		$lastMessageCreationDateElement = $this->buildChatLastMessageCreationTime($lastMessage->getCreatedAt());
		$lastMessageContainerElement = $this->buildChatLastMessageContainer(
			$lastMessageContentElement,
			$lastMessageCreationDateElement,
			$lastMessageAuthorElement,
			$chatId
		);
	
		$item = HTML::li('', 'diomsg-user-chat-' . $chatId, 'diomsg-user-chat');
		HTML::append($buttonContainer, $deleteChatButton);
		
		$buttonAndMessageContainer = HTML::div('', '', 'diomsg-user-chat-list-bm-container');
		HTML::append($buttonAndMessageContainer, $lastMessageContainerElement);
		HTML::append($buttonAndMessageContainer, $buttonContainer);
		
		HTML::append($item, $participantsElement);
		HTML::append($item, $buttonAndMessageContainer);
		
		return $item;
	}
	
	/**
	 * {description}
	 * 
	 * @return	UserChatListFormBuilder
	 * 		{description}
	 */
	public function buildList() {
		$this->list = HTML::ul('', 'diomsg-user-chat-list');
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	UserChatListFormBuilder
	 * 		{description}
	 */
	public function buildSelectedChatField() {
		$field = $this->form->getInput('hidden', GetMessagesFormBuilder::FIELD_NAME_CHAT_ID);
		$this->form->append($field);
	
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function get() {
		return $this->container;
	}
	
	public function getActionView() {
		return 'chat/Open';
	}
	
	public function getChatIdFromChatListItem($chatListItem) {
		$chat = $chatListItem['chat'];
		
		return $chat->getId();
	}
	
	private function buildButtonContainer() {
		return HTML::div('', '', 'diomsg-user-chat-list-button-container');
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$authorId
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	private function buildChatLastMessageAuthor($authorId) {
		$name = $authorId === $this->userAccountId
			? appLiteral::get('chat', 'util_you', array(), false)
			: $this->chatManager->findChatParticipantNameFromAccount($authorId);
	
		return HTML::span($name . ' said:', '', 'diomsg-user-chat-list-item-message-author');
	}
	
	private function buildDeleteChatButton($chatId) {
		$icon = HTML::i('', '', 'icon icon-trash');
		$button = HTML::a(
			'',
			'diomsg-user-chat-list-delete-chat-button-' . $chatId,
			'diomsg-user-chat-list-button diomsg-chat-button'
		);
		HTML::attr($button, 'title', appLiteral::get('chat', 'delete'));
		HTML::append($button, $icon);
		
		$this->actionFactory->setAction(
			$button,
			'chat/Delete',
			'',
			array(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chatId),
			false
		);
		
		return $button;
	}
	
	/**
	 * {description}
	 * 
	 * @param	DOMElement	$messageContentContainer
	 * 		{description}
	 * 
	 * @param	DOMElement	$messageCreationTimeContainer
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	private function buildChatLastMessageContainer(
			$messageContentContainer,
			$messageCreationTimeContainer,
			$messageAuthorContainer,
			$chatId) {
		$container = HTML::div('', '', 'diomsg-user-chat-list-item-container');
		HTML::append($container, $messageAuthorContainer);
		HTML::append($container, $messageContentContainer);
		HTML::append($container, $messageCreationTimeContainer);
		
		$this->actionFactory->setAction(
			$container,
			'chat/Open',
			'',
			array(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chatId),
			false
		);
		
		return $container;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$content
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	private function buildChatLastMessageContent($content) {
		return HTML::p($content, '', 'diomsg-user-chat-list-item-message-content');
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$time
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	private function buildChatLastMessageCreationTime($time) {
		return HTML::span('Sent at ' . $time, '', 'diomsg-user-chat-list-item-message-created-at');
	}
	
	private function buildChatParticipantsElement($chat) {
		$participants = array_filter(
			$chat->getParticipants(),
			function($participant) {
				return $participant->getAccountId() !== $this->userAccountId;
			}
		);
		$names = array();
		foreach ($participants as $p) {
			$names[] = $p->getName();
		}
		$namesString = implode(', ', $names);
		
		return HTML::div($namesString, '', 'diomsg-user-chat-list-participants-list');
	}
	
	private function buildChatUpdated($updated) {
		$icon = HTML::i('', '', 'icon icon-refresh diomsg-util-FlL diomsg-util-MarTBN');
		$text = HTML::span(appLiteral::get('chat', 'updated', array(), false), '', '');
		$container = HTML::div('', '', 'diomsg-user-chat-list-chat-updated');
		
		HTML::append($container, $icon);
		HTML::append($container, $text);
		
		return $container;
	}
	
	private function buildOpenChatButton($chatId) {
		$button = HTML::a(
			appLiteral::get('chat', 'open'),
			'diomsg-user-chat-list-open-chat-button-' . $chatId,
			'diomsg-user-chat-list-button'
		);
		$this->actionFactory->setAction(
			$button,
			'chat/Open',
			'',
			array(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chatId),
			false
		);
		
		return $button;
	}
}
//#section_end#
?>