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
importer::import("API", "Platform", "engine");
application::import('Main', 'GetMessagesFormBuilder');

use API\Platform\engine;
use APP\Main\GetMessagesFormBuilder;

class GetChatMessagesRequest {

	private $chatId;
	
	public static function fromEngine() {
		$chatId = engine::getVar(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID);
		
		return new GetChatMessagesRequest($chatId);
	}

	public function __construct($chatId) {
		$this->chatId = $chatId;
	}
	
	public function getChatId() {
		return $this->chatId;
	}
}
//#section_end#
?>