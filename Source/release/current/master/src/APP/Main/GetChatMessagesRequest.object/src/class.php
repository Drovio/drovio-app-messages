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

use API\Platform\engine;
use APP\Main\GetMessagesFormBuilder;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:43 (EEST)
 * @updated	September 29, 2015, 20:43 (EEST)
 * 
 * @deprecated	A polling mechanism is used in place of simple GET requests. See the PollMessagesRequest class.
 */
class GetChatMessagesRequest {

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $chatId;
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public static function fromEngine() {
		$chatId = engine::getVar(GetMessagesFormBuilder::FIELD_NAME_CHAT_ID);
		
		return new GetChatMessagesRequest($chatId);
	}

	/**
	 * {description}
	 * 
	 * @param	{type}	$chatId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct($chatId) {
		$this->chatId = $chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function getChatId() {
		return $this->chatId;
	}
}
//#section_end#
?>