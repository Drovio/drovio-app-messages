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
application::import('Main', 'DeleteChatFormBuilder');

use API\Platform\engine;
use APP\Main\DeleteChatFormBuilder;

/**
 * Represents the response generated after a DeleteChatRequest has been processed.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:41 (EEST)
 * @updated	September 29, 2015, 20:41 (EEST)
 */
class DeleteChatResponse {
 
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $chatId;

	/**
	 * {description}
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat to be deleted.
	 * 
	 * @return	void
	 */
	public function __construct($chatId) {
		$this->chatId = $chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		The ID of the Chat to be deleted.
	 */
	public function getChatId() {
		return $this->chatId;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this response. The array  format is as follows:
	 * 		
	 * 		  array(
	 * 		    DeleteChatFormBuilder::FIELD_NAME_CHAT_ID => <chat ID>
	 * 		  )
	 */
	public function toArray() {
		return array(
			DeleteChatFormBuilder::FIELD_NAME_CHAT_ID => $this->getChatId()
		);
	}
}
//#section_end#
?>