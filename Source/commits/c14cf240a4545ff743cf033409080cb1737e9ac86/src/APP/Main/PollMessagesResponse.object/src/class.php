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
class PollMessagesResponse {

	const KEY_MESSAGES = 'messages';
	
	private $content;

	public function __construct() {
		$this->content = array();
	}
	
	public function buildMessages(array $messages) {
		if (isset($this->content[self::KEY_MESSAGES])) {
			return $this;
		}
		
		$normalizedMessages = array();
		foreach ($messages as $message) {
			$normalizedMessages[$message->getId()] = $message->toArray();
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