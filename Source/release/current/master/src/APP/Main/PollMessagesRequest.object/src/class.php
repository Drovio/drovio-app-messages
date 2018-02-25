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
/**
 * @library	APP
 * @package	Main
 * 
 * @copyright	Copyright (C) 2015 Messages. All rights reserved.
 */

importer::import("API", "Platform", "engine");
application::import('Main', 'GetMessagesFormBuilder');
application::import('Main', 'PollMessagesFormBuilder');

use API\Platform\engine;
use APP\Main\PollMessagesFormBuilder;
use APP\Main\GetMessagesFormBuilder;

/**
 * Represents a request for message polling.
 * 
 * See the PendingMessage class description for the definition of the Message Polling Mechanism.
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 22:46 (EEST)
 * @updated	September 29, 2015, 22:46 (EEST)
 */
class PollMessagesRequest {

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $chatId;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $participantId;
	
	/**
	 * Creates a new PollMessagesRequest with data retrieved from the "engine".
	 * 
	 * @return	PollMessagesRequest
	 * 		{description}
	 */
	public static function fromEngine() {
		$chatId = engine::getVar(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID);
		$participantId = engine::getVar(PollMessagesFormBuilder::FIELD_NAME_PARTICIPANT_ID);
		
		return new PollMessagesRequest($participantId, $chatId);	
	}

	/**
	 * {description}
	 * 
	 * @param	integer	$participantId
	 * 		The account ID of the ChatParticipant the polling occurs for.
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat the polling occurs for.
	 * 
	 * @return	void
	 */
	public function __construct($participantId, $chatId) {
		$this->participantId = $participantId;
		$this->chatId = $chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The ID of the Chat the polling occurs for.
	 */
	public function getChatId() {
		return $this->chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		The account ID of the ChatParticipant the polling occurs for.
	 */
	public function getParticipantId() {
		return $this->participantId;
	}
}
//#section_end#
?>