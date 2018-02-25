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
importer::import('API', 'Platform', 'engine');
 
use API\Platform\engine;

class CloseChatRequest {
 
 	const KEY_COMPLETE_CLOSE = 'complete_close';

	const FIELD_NAME_CHAT_ID = 'diomsg-chat-id';
 
	private $chatId;

 	private $completeClose;

 	public static function fromEngine() {
		$completeClose = (bool) engine::getVar(self::KEY_COMPLETE_CLOSE);
		$chatId = engine::getVar(self::FIELD_NAME_CHAT_ID);
	
		return new CloseChatRequest($completeClose, $chatId);
	}

	public function __construct($completeClose, $chatId) {
		$this->completeClose = $completeClose;
		$this->chatId = $chatId;
	}
	
	public function getChatId() {
		return $this->chatId;
	}
	
	public function isCompleteClose() {
		return $this->completeClose;
	}
}
//#section_end#
?>