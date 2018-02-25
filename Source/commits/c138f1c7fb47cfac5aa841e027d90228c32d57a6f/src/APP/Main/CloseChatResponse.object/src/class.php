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
application::import('APP', 'Main', 'CloseChatRequest');

use API\Platform\engine;
use APP\Main\CloseChatRequest;

class CloseChatResponse {

	private $completeClose;

	public function __construct($completeClose) {
		$this->completeClose = $completeClose;
	}
	
	public function isCompleteClose() {
		return $this->completeClose;
	}
	
	public function toArray() {
		return array(
			CloseChatRequest::KEY_COMPLETE_CLOSE => $this->isCompleteClose()
		);
	}
}
//#section_end#
?>