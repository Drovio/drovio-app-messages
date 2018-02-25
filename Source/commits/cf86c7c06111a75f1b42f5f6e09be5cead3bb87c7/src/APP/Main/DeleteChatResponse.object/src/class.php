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
application::import('APP', 'Main', 'DeleteChatFormBuilder');

use API\Platform\engine;
use APP\Main\DeleteChatFormBuilder;

class DeleteChatResponse {
 
	private $chatId;

	public function __construct($chatId) {
		$this->chatId = $chatId;
	}
	
	public function getChatId() {
		return $this->chatId;
	}
	
	public function toArray() {
		return array(
			DeleteChatFormBuilder::FIELD_NAME_CHAT_ID => $this->getChatId()
		);
	}
}
//#section_end#
?>