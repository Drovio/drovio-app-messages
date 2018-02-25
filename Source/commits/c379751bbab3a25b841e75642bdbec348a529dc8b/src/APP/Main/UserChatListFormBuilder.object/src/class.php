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
application::import('Main', 'ChatManager');

use \UI\Forms\Form;
use \UI\Html\HTML;
use \APP\Main\ChatManager;
use \AEL\Literals\appLiteral;

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
	public function __construct(ChatManager $chatManager, $userAccountId) {
		$this->chatManager = $chatManager;
		$this->userAccountId = $userAccountId;
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
		HTML::append($this->container, $this->form->get());
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
			->engageApp('chat/Open');
			
		return $this;
	}
	
	/**
	 * {description}
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
	 * 		{description}
	 */
	public function buildItem($chatListItem) {
		$chat = $chatListItem['chat'];
		$lastMessage = $chatListItem['lastMessage'];
		
		$lastMessageAuthorElement = $this->buildChatLastMessageAuthor($lastMessage->getAuthor());
		$lastMessageContentElement = $this->buildChatLastMessageContent($lastMessage->getContent());
		$lastMessageCreationDateElement = $this->buildChatLastMessageCreationTime($lastMessage->getCreatedAt());
		$lastMessageContainerElement = $this->buildChatLastMessageContainer(
			$lastMessageContentElement,
			$lastMessageCreationDateElement
		);
	
		$item = HTML::li('', 'diomsg-user-chat-' . $chat->getId(), 'diomsg-user-chat');
		HTML::append($item, $lastMessageAuthorElement);
		HTML::append($item, $lastMessageContainerElement);
		HTML::append($this->list, $item);
		
		return $this;
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
		$field = $this->form->getInput('hidden', 'diomsg-user-chat-list-selected-chat');
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
	private function buildChatLastMessageContainer($messageContentContainer, $messageCreationTimeContainer) {
		$container = HTML::div('', '', 'diomsg-user-chat-list-item-container');
		HTML::append($container, $messageContentContainer);
		HTML::append($container, $messageCreationTimeContainer);
		
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
}
//#section_end#
?>