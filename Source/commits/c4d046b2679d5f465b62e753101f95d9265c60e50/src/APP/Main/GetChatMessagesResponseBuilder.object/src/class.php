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
importer::import('UI', 'Html', 'HTML');
application::import('Main', 'ChatMessage');
application::import('Main', 'ChatManager');
importer::import("AEL", "Literals", "appLiteral");

use \APP\Main\ChatMessage;
use \APP\Main\ChataManager;
use \UI\Html\HTML;
use \AEL\Literals\appLiteral;

class GetChatMessagesResponseBuilder {
	
	private $authorNameList;
	
	private $chatManager;
	
	private $list;
	
	private $userAccountId;	
	
	public function __construct(ChatManager $chatManager, $userAccountId) {
		$this->authorNameList = array();
		$this->chatManager = $chatManager;
		$this->userAccountId = $userAccountId;
	}
	
	public function buildChatMessageList($chatId) {
		$this->list = HTML::ul('', 'diomsg-get-chat-messages-list-' . $chatId);
		
		return $this;
	}
	
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
	
	public function get() {
		return $this->list;
	}
	
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