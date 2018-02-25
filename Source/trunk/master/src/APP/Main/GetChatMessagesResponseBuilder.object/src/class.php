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

importer::import('UI', 'Html', 'HTML');
application::import('Main', 'ChatMessage');
application::import('Main', 'ChatManager');
importer::import("AEL", "Literals", "appLiteral");

use \APP\Main\ChatMessage;
use \APP\Main\ChataManager;
use \UI\Html\HTML;
use \AEL\Literals\appLiteral;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-2
 * @created	September 29, 2015, 20:45 (EEST)
 * @updated	September 29, 2015, 20:46 (EEST)
 * 
 * @deprecated	A polling mechanism is used in place of simple GET requests.
 */
class GetChatMessagesResponseBuilder {
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $authorNameList;
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $chatManager;
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $list;
	
	/**
	 * {description}
	 * 
	 * @type	{type}
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
		$this->authorNameList = array();
		$this->chatManager = $chatManager;
		$this->userAccountId = $userAccountId;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$chatId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function buildChatMessageList($chatId) {
		$this->list = HTML::ul('', 'diomsg-get-chat-messages-list-' . $chatId);
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$message
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function buildChatMessage(ChatMessage $message) {
		$authorName = $this->getAuthorName($message->getAuthor());
	
		$authorNameEl = HTML::span($authorName, '', 'diomsg-message-author');
		$contentEl = HTML::p($message->getContent(), '', 'diomsg-message');
		$createdAtEl = HTML::span($message->getCreatedAt(), '', 'diomsg-message-created-at');
		
		$item = HTML::li('', '', 'diomsg-message-container');
		HTML::append($item, $authorNameEl);
		HTML::append($item, $contentEl);
		HTML::append($item, $createdAtEl);
		
		HTML::append($this->list, $item);
	
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function get() {
		return $this->list;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$authorId
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function getAuthorName($authorId) {
		if ($authorId === $this->userAccountId) {
			return appLiteral::get('chat', 'util_me', array(), false);
		}
	
		if (isset($this->authorNameList[$authorId])) {
			return $this->authorNameList[$authorId];
		}
		
		$name = $this->chatManager->findChatParticipantNameFromAccount($authorId);
		
		return $this->authorNameList[$authorId] = $name;
	}
}
//#section_end#
?>