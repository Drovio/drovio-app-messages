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

application::import('Main', 'Chat');

use \APP\Main\Chat;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	1.0-1
 * @created	August 29, 2015, 11:33 (EEST)
 * @updated	August 29, 2015, 11:36 (EEST)
 */
class ChatMessage {

	/**
	 * The Chat this message belongs to.
	 * 
	 * @type	Chat
	 */
	private $chat;

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	private $content;
	
	/**
	 * {description}
	 * 
	 * @param	Chat	$chat
	 * 		{description}
	 * 
	 * @param	string	$content
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct($chat, $content) {
		$this->chat = $chat;
		$this->content = $content;
	}
	
	/**
	 * {description}
	 * 
	 * @return	Chat
	 * 		{description}
	 */
	public function getChat() {
		return $this->chat;
	}
	
	/**
	 * {description}
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getContent() {
		return $this->content;
	}
}
//#section_end#
?>