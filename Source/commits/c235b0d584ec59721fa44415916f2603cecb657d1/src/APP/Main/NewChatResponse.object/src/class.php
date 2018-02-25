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
application::import('Main', 'Chat');

use APP\Main\Chat;

class NewChatResponse {

	private $chat;
	
	// Constructor Method
	public function __construct(Chat $chat) {
		$this->chat = $chat;
	}
	
	public function isSuccess() {
		return $this->chat->getId() !== null;
	}
	
	public function toArray() {
		$success = $this->isSuccess();
		
		return array(
		  "success" => $success,
		  "message" => $this->buildMessage($success),
		  "chatId" => $this->chat->getId(),
		  "ownerId" => $this->chat->getOwner()->getId(),
		  "recipientsIds" => $this->buildRecipientsIds(),
		  "teamId" => $this->chat->getTeam()->getId()
		);
	}
	
	private function buildMessage($success) {
		return $success
			? "You have started a new chat with: " . $this->buildRecipientsNames()
			: "A new chat could not be started.";
	}
	
	private function buildRecipientsIds() {
		$recipients = $this->chat->getRecipients();
		
		return array_map(
			function($recipient) {
				return $recipient->getId();
			},
			$recipients
		);
	}
	
	private function buildRecipientsNames() {
		$recipients = $this->chat->getRecipients();
		
		return implode(
			', ',
			array_map(
				function($recipient) {
					return $recipient->getName();
				},
				$recipients
			)
		);
	}
}
//#section_end#
?>