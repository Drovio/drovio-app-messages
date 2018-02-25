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
importer::import("UI", "Forms", "Form");
importer::import("AEL", "Literals", "appLiteral");
application::import("APP", "Main", "Chat");

use \UI\Forms\Form;
use \AEL\Literals\appLiteral;
use \APP\Main\Chat;

class ChatMessageFormBuilder {

	private static $FIELD_NAME_CHAT_ID = "diomsg-chat-id";
	
	private static $FIELD_NAME_MESSAGE = "diomsg-message";

	private $chat;

	private $form;
	
	private $message;

	// Constructor Method
	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	public function buildSendButton() {
		$button = $this->form->getSubmitButton(
			appLiteral::get('chat', 'button_send_chat_message'), 
			'diomsg-send-chat-message-button'
		);
		$this->form->append($button);
	
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
	
	public function withChat(Chat $chat) {
		if ($this->chat !== null) {
			return $this;
		}
		
		$this->chat = $chat;
		$input = $this->form->getInput("hidden", static::$FIELD_NAME_CHAT_ID, $chat->getId());
		$this->form->append($input);
		
		return $this;
	}
	
	public function withMessage($message) {
		if ($this->message !== null) {
			return $this;
		}
		
		$this->message = $message;
		$input = $this->form->getTextarea(static::$FIELD_NAME_MESSAGE, $message, "diomsg-message-input-field");
		$input->setAttribute("rows", 4);
		$input->setAttribute("cols", 40);
		$this->form->append($input);
		
		return $this;
	}
}
//#section_end#
?>