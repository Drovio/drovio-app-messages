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
application::import('Main', 'ChatMessageFormBuilder');

use APP\Main\Chat;
use \APP\Main\ChatMessageFormBuilder;

/**
 * Represents the response generated after a NewChatRequest has been processed.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 21:20 (EEST)
 * @updated	September 29, 2015, 21:20 (EEST)
 */
class NewChatResponse {

	/**
	 * {description}
	 * 
	 * @type	Chat
	 */
	private $chat;
	
	/**
	 * {description}
	 * 
	 * @type	ChatMessageFormBuilder
	 */
	private $chatMessageFormBuilder;
	
	/**
	 * {description}
	 * 
	 * @param	Chat	$chat
	 * 		The new Chat.
	 * 
	 * @param	ChatMessageFormBuilder	$cmfb
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(Chat $chat, ChatMessageFormBuilder $cmfb) {
		$this->chat = $chat;
		$this->chatMessageFormBuilder = $cmfb;
	}
	
	/**
	 * {description}
	 * 
	 * @return	boolean
	 * 		A value of "true" indicates that the new Chat has been created successfully.
	 */
	public function isSuccess() {
		return $this->chat->getId() !== null;
	}
	
	/**
	 * {description}
	 * 
	 * @return	array
	 * 		The array representation of this response. The array  format is as follows:
	 * 		
	 * 		  array(
	 * 		    'success' => boolean,
	 * 		    'message' => <the return value of the "buildMessage" method>:string,
	 * 		    'chatId' => string,
	 * 		    'ownerId' => integer,
	 * 		    'recipientsIds' => integer[],
	 * 		    'teamId' => integer
	 * 		  )
	 */
	public function toArray() {
		$success = $this->isSuccess();
		
		return array(
		  "success" => $success,
		  "message" => $this->buildMessage($success),
		  "chatId" => $this->chat->getId(),
		  "ownerId" => $this->chat->getOwner(),
		  "recipientsIds" => $this->chat->getRecipients(),
		  "teamId" => $this->chat->getTeam()
		);
	}
	
	/**
	 * {description}
	 * 
	 * @param	boolean	$success
	 * 		A value of "true" indicates that the Chat has been created successfully.
	 * 
	 * @return	string
	 * 		A failure message if "success" is false. Otherwise, an empty string.
	 */
	private function buildMessage($success) {
		return $success
			? ""
			: "A new chat could not be started.";
	}
}
//#section_end#
?>