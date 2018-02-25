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
application::import('APP', 'Main', 'DeleteChatFormBuilder');

use API\Platform\engine;
use APP\Main\DeleteChatFormBuilder;

/**
 * Represents a request for deleting a Chat.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:39 (EEST)
 * @updated	September 29, 2015, 20:39 (EEST)
 */
class DeleteChatRequest {
 
	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $chatId;
	
	/**
	 * {description}
	 * 
	 * @type	boolean
	 */
	private $deleteIfEmpty;
	
	/**
	 * Creates a new DeleteChatRequest with data retrieved from the "engine".
	 * 
	 * @return	DeleteChatRequest
	 * 		{description}
	 */
	public static function fromEngine() {
		$chatId = engine::getVar(DeleteChatFormBuilder::FIELD_NAME_CHAT_ID);
		$deleteIfEmpty = intval(engine::getVar(DeleteChatFormBuilder::FIELD_NAME_DELETE_IF_EMPTY)) === 1 ? true : false;
		
		return new DeleteChatRequest($chatId, $deleteIfEmpty);
	}

	/**
	 * {description}
	 * 
	 * @param	string	$chatId
	 * 		The ID of the Chat to be deleted.
	 * 
	 * @param	boolean	$deleteIfEmpty
	 * 		A value of "true" indicates that the Chat should be deleted only if it is empty.
	 * 		
	 * 		See the DeleteChatFormBuilder::buildDeleteChatIfEmptyField method for the definition of an empty Chat.
	 * 
	 * @return	void
	 */
	public function __construct($chatId, $deleteIfEmpty) {
		$this->chatId = $chatId;
		$this->deleteIfEmpty = $deleteIfEmpty;
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
	 * @return	boolean
	 * 		A value of "true" indicates that the Chat should be deleted only if it is empty.
	 * 		
	 * 		See the DeleteChatFormBuilder::buildDeleteChatIfEmptyField method for the definition of an empty Chat.
	 */
	public function isDeleteIfEmpty() {
		return $this->deleteIfEmpty;
	}
}
//#section_end#
?>