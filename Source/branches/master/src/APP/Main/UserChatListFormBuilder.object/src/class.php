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
 * Creates the list of Chats in which the currently logged-in user is a ChatParticipant.
 * 
 * {description}
 * 
 * @version	3.0-2
 * @created	September 7, 2015, 13:30 (EEST)
 * @updated	September 29, 2015, 23:18 (EEST)
 */
class UserChatListFormBuilder {

	/**
	 * {description}
	 * 
	 * @type	AppActionFactory
	 */
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
	 * @param	ChatManager	$chatManager
	 * 		{description}
	 * 
	 * @param	integer	$userAccountId
	 * 		The ID of the currently logged-in user.
	 * 
	 * @param	AppActionFactory	$actionFactory
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
	 * The "Container" is the element that contains the whole "User Chat List".
	 * 
	 * @return	UserChatListFormBuilder
	 * 		$this
	 */
	public function buildContainer() {
		$this->container = HTML::div('', 'diomsg-user-chat-list-container');
		HTML::append($this->container, $this->list);
		
		return $this;
	}
	
	/**
	 * Engages the form to the "chat/Open" view.
	 * 
	 * @param	Form	$form
	 * 		{description}
	 * 
	 * @return	UserChatListFormBuilder
	 * 		$this
	 */
	public function buildForm(Form $form) {
		$this->form = $form;
		
		$this->form->build()
			->engageApp($this->getActionView());
			
		return $this;
	}
	
	/**
	 * Creates an element that holds a single Chat in the "User Chat List".
	 * 
	 * @param	array	$chatListItem
	 * 		The format of this array is:
	 * 		
	 * 		    array(
	 * 		      'chat' => Chat,
	 * 		      'lastMessage' => ChatMessage
	 * 		    )
	 * 
	 * @return	UserChatListFormBuilder
	 * 		$this
	 */
	public function buildItem($chatListItem) {
		$item = $this->buildItemWith($chatListItem);
		HTML::append($this->list, $item);
		
		return $this;
	}
	
	/**
	 * Creates the element that holds the list of items created by the "builditem" method.
	 * 
	 * @return	UserChatListFormBuilder
	 * 		$this
	 */
	public function buildList() {
		$this->list = HTML::ul('', 'diomsg-user-chat-list');
		
		return $this;
	}
	
	/**
	 * Adds a hidden field that contains the ID of the Chat that has been selected for opening.
	 * 
	 * @return	UserChatListFormBuilder
	 * 		$this
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
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The "chat/Open" view path.
	 */
	public function getActionView() {
		return 'chat/Open';
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$chatListItem
	 * 		{description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getChatIdFromChatListItem($chatListItem) {
		$chat = $chatListItem['chat'];
		
		return $chat->getId();
	}
	
	/**
	 * {description}
	 * 
	 * @return	DOMElement
	 * 		An element that contains all the buttons concerning a Chat in the list.
	 */
	private function buildButtonContainer() {
		return HTML::div('', '', 'diomsg-user-chat-list-button-container');
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$authorId
	 * 		The author's account ID.
	 * 
	 * @return	DOMElement
	 * 		An element that contains the author of a Chat's last message.
	 */
	private function buildChatLastMessageAuthor($authorId) {
		$name = $authorId === $this->userAccountId
			? appLiteral::get('chat', 'util_you', array(), false)
			: $this->chatManager->findChatParticipantNameFromAccount($authorId);
	
		return HTML::span($name, '', 'diomsg-user-chat-list-item-message-author');
	}
	
	private function buildChatLastMessageContainer(
			$messageContentContainer,
			$messageCreationTimeContainer,
			$messageAuthorContainer,
			$chatId,
			$active) {
		$cls = $active ? ' active' : '';
		$container = HTML::div('', '', 'diomsg-user-chat-list-item-container' . $cls);
		HTML::append($container, $messageAuthorContainer);
		HTML::append($container, $messageCreationTimeContainer);
		HTML::append($container, $messageContentContainer);
		
//		$this->actionFactory->setAction(
//			$container,
//			'chat/Open',
//			'',
//			array(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chatId),
//			false
//		);
		
		return $container;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$content
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		An element that contains the content of a Chat's last message.
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
	 * 		An element that contains the creation time of a Chat's last message.
	 */
	private function buildChatLastMessageCreationTime($time) {
		return HTML::span($time, '', 'diomsg-user-chat-list-item-message-created-at');
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$chat
	 * 		The Chat ID.
	 * 
	 * @return	DOMElement
	 * 		An element that contains a comma-separated list of a Chat's participants.
	 */
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
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$updated
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	private function buildChatUpdated($updated) {
		$icon = HTML::i('', '', 'icon icon-refresh diomsg-util-FlL diomsg-util-MarTBN');
		$text = HTML::span(appLiteral::get('chat', 'updated', array(), false), '', '');
		$container = HTML::div('', '', 'diomsg-user-chat-list-chat-updated');
		
		HTML::append($container, $icon);
		HTML::append($container, $text);
		
		return $container;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$chatId
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	private function buildDeleteChatButton($chatId) {
		$form = new Form('diomsg-user-chat-list-delete-chat-form-' . $chatId);
		$form->build()->engageApp('chat/Delete');
		
		$chatIdField = $form->getInput('hidden', GetMessagesFormBuilder::FIELD_NAME_CHAT_ID, $chatId);
		$form->append($chatIdField);
		
		$deleteButton = $form->getSubmitButton('', 'diomsg-user-chat-list-delete-chat-button-' . $chatId);
	 	$icon = HTML::i('', '', 'icon icon-trash');
		HTML::append($deleteButton, $icon);
 		$form->append($deleteButton);
		
		return $form->get();
		
//		$icon = HTML::i('', '', 'icon icon-trash');
//		$button = HTML::a(
//			'',
//			'diomsg-user-chat-list-delete-chat-button-' . $chatId,
//			'diomsg-user-chat-list-button diomsg-chat-button'
//		);
//		HTML::attr($button, 'title', appLiteral::get('chat', 'delete'));
//		HTML::append($button, $icon);
//		
//		$this->actionFactory->setAction(
//			$button,
//			'chat/Delete',
//			'',
//			array(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chatId),
//			false
//		);
//		
//		return $button;
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$chatListItem
	 * 		An array containing the Chat and the its last message. The format is:
	 * 		
	 * 		  array(
	 * 		    'chat' => Chat,
	 * 		    'lastMessage' => ChatMessage
	 * 		  )
	 * 
	 * @return	DOMElement
	 * 		An element that corresponds to a Chat in the list.
	 */
	private function buildItemWith($chatListItem) {
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
			$chatId,
			$chat->isActive()
		);
	
		$itemCls = 'diomsg-user-chat';
		$itemCls .= $chat->isActive() ? ' active' : '';
		$item = HTML::li('', 'diomsg-user-chat-' . $chatId, $itemCls);
		$this->actionFactory->setAction(
			$item,
			'chat/Open',
			'',
			array(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chatId),
			false
		);
		
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
	 * @param	string	$chatId
	 * 		{description}
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
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