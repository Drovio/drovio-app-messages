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
class NewChatMessageResponse {

	private $chatId;
	
	private $firstMessage;

	public function __construct() {
	}
	
	public function buildChatId($chatId) {
		$this->chatId = $chatId;
		
		return $this;
	}
	
	public function buildFirstMessage($value) {
		$this->firstMessage = $value;
		
		return $this;
	}
	
	public function getChatId() {
		return $this->chatId;
	}
	
	public function isFirstMessage() {
		return $this->firstMessage;
	}
	
	public function toArray() {
		return array(
			'chat_id' => $this->getChatId(),
			'first_message' => $this->isFirstMessage()
		);
	}
}
//#section_end#
?>