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
 
 	private $completeClose;

 	public static function fromEngine() {
		$completeClose = (bool) engine::getVar(self::KEY_COMPLETE_CLOSE);
	
		return new CloseChatRequest($completeClose);
	}

	public function __construct($completeClose) {
		$this->completeClose = $completeClose;
	}
	
	public function isCompleteClose() {
		return $this->completeClose;
	}
}
//#section_end#
?>