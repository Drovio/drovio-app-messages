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

use API\Platform\engine;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-2
 * @created	August 31, 2015, 14:41 (EEST)
 * @updated	August 31, 2015, 14:42 (EEST)
 */
class NewChatRequest {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	const NO_RECIPIENT_ID = 0;

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $ownerId;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $recipientsIds;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $teamId;
	
	/**
	 * {description}
	 * 
	 * @return	NewChatRequest
	 * 		{description}
	 */
	public static function fromEngine() {
		$ownerId = engine::getVar("diomsg-owner");
		$recipientsIds = array(engine::getVar("diomsg-contact"));
		$teamId = engine::getVar("diomsg-team");
		
		return new NewChatRequest($ownerId, $recipientsIds, $teamId);
	}

	/**
	 * {description}
	 * 
	 * @param	integer	$ownerId
	 * 		{description}
	 * 
	 * @param	integer	$recipientsIds
	 * 		{description}
	 * 
	 * @param	integer	$teamId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct($ownerId, array $recipientsIds, $teamId) {
		$this->ownerId = $ownerId;
		$this->recipientsIds = $recipientsIds;
		$this->teamId = $teamId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
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
	 * @return	integer
	 * 		{description}
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