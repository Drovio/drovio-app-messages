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

use \APP\Main\ChatMessage;
use \UI\Html\HTML;

class GetChatMessagesResponseBuilder {

	private $list;
	
	public function buildChatMessageList() {
		$this->list = HTML::ul('', 'diomsg-get-chat-messages-list');
		
		return $this;
	}
	
	public function buildChatMessage(ChatMessage $message) {
		$contentEl = HTML::p($message->getContent());
		$createdAtEl = HTML::span($message->getCreatedAt());
		
		$item = HTML::li('');
		HTML::append($item, $contentEl);
		HTML::append($item, $createdAtEl);
		
		HTML::append($this->list, $item);
	
		return $this;
	}
	
	public function get() {
		return $this->list;
	}
}
//#section_end#
?>