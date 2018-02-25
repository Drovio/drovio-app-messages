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

class DeleteChatRequest {
 
	private $chatId;
	
	private $deleteIfEmpty;
	
	public static function fromEngine() {
		$chatId = engine::getVar(DeleteChatFormBuilder::FIELD_NAME_CHAT_ID);
		$deleteIfEmpty = intval(engine::getVar(DeleteChatFormBuilder::FIELD_NAME_DELETE_IF_EMPTY)) === 1 ? true : false;
		
		return new DeleteChatRequest($chatId, $deleteIfEmpty);
	}

	public function __construct($chatId, $deleteIfEmpty) {
		$this->chatId = $chatId;
		$this->deleteIfEmpty = $deleteIfEmpty;
	}
	
	public function getChatId() {
		return $this->chatId;
	}
	
	public function isDeleteIfEmpty() {
		return $this->deleteIfEmpty;
	}
}
//#section_end#
?>