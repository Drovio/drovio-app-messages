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
application::import("Main", "ChatMessageFormBuilder");

use API\Platform\engine;
use APP\Main\ChatMessageFormBuilder;

/**
 * Represents a request for creating a new Chat.
 * 
 * {description}
 * 
 * @version	1.0-1
 * @created	August 31, 2015, 14:41 (EEST)
 * @updated	September 29, 2015, 21:15 (EEST)
 */
class NewChatRequest {

	/**
	 * An ID that indicates that no recipients have been specified for the new Chat. See the Chat::getRecipients method for the definition of a Chat recipient.
	 * 
	 * @type	integer
	 */
	const NO_RECIPIENT_ID = 0;
	
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $firstMessageContent;

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $ownerId;
	
	/**
	 * An array of integers.
	 * 
	 * @type	array
	 */
	private $recipientsIds;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $teamId;
	
	/**
	 * Creates a new NewChatRequest with data retrieved from the "engine".
	 * 
	 * @return	NewChatRequest
	 * 		{description}
	 */
	public static function fromEngine() {
		$ownerId = engine::getVar("diomsg-owner");
		$recipientsIds = array(engine::getVar("diomsg-contact"));
		$teamId = engine::getVar("diomsg-team");
		$firstMessageContent = engine::getVar(ChatMessageFormBuilder::FIELD_NAME_MESSAGE);
		
		return new NewChatRequest($ownerId, $recipientsIds, $teamId, $firstMessageContent);
	}

	/**
	 * {description}
	 * 
	 * @param	integer	$ownerId
	 * 		{description}
	 * 
	 * @param	array	$recipientsIds
	 * 		An array of integers that contains the IDs of the new Chat's recipients.
	 * 		
	 * 		See the Chat::getRecipients method for the definition of a new Chat.
	 * 
	 * @param	integer	$teamId
	 * 		{description}
	 * 
	 * @param	string	$firstMessageContent
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct($ownerId, array $recipientsIds, $teamId, $firstMessageContent) {
		$this->ownerId = $ownerId;
		$this->recipientsIds = $recipientsIds;
		$this->teamId = $teamId;
		$this->firstMessageContent = $firstMessageContent;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getFirstMessageContent() {
		return $this->firstMessageContent;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		The ID of the first recipient of the new Chat.
	 * 		
	 * 		See the Chat::getRecipients method for the definition of a new Chat.
	 */
	public function getFirstRecipientId() {
		if (empty($this->recipientsIds)) {
			return self::NO_RECIPIENT_ID;
		}
		
		return $this->recipientsIds[0];
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getOwnerId() {
		return $this->ownerId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		An array of integers that contains the IDs of the new Chat's recipients.
	 * 		
	 * 		See the Chat::getRecipients method for the definition of a new Chat.
	 */
	public function getRecipientsIds() {
		return $this->recipientsIds;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getTeamId() {
		return $this->teamId;
	}
}
//#section_end#
?>