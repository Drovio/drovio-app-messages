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
class OpenChatResponse {

	const KEY_CHAT_ID = 'chat_id';

	const KEY_MESSAGES = 'messages';
	
	private $content;
	
	private $userAccountId;

	public function __construct($userAccountId) {
		$this->content = array();
		$this->userAccountId = $userAccountId;
	}
	
	public function buildChat($chatId) {
		if (!isset($this->content[self::KEY_CHAT_ID])) {
			$this->content[self::KEY_CHAT_ID] = $chatId;
		}
		
		return $this;
	}
	
	public function buildMessages(array $messages) {
		if (isset($this->content[self::KEY_MESSAGES])) {
			return $this;
		}
		
		$normalizedMessages = array();
		foreach ($messages as $message) {
			$normalizedMessages[] = $message->toArray();
		}
		$this->content[self::KEY_MESSAGES] = $normalizedMessages;
		
		return $this;
	}
	
	public function toArray() {
		return $this->content;
	}
}
//#section_end#
?>