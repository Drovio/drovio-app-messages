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
importer::import("API", "Profile", "account");
application::import("APP", "Main", "Chat");
application::import("APP", "Main", "ChatParticipant");

use \UI\Forms\Form;
use \AEL\Literals\appLiteral;
use \APP\Main\Chat;
use \API\Profile\account;
use \APP\Main\ChatParticipant;

class ChatMessageFormBuilder {

	const FIELD_NAME_AUTHOR_ID = "diomsg-author-id";

	const FIELD_NAME_CHAT_ID = "diomsg-chat-id";
	
	const FIELD_NAME_FIRST_MESSAGE = "diomsg-first-message";
	
	const FIELD_NAME_MESSAGE = "diomsg-message";
	
	private $authorId;

	private $chat;

	private $form;
	
	private $message;
	
	private $sendButton;

	// Constructor Method
	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	public function buildFirstMessage() {
		$input = $this->form->getInput('hidden', self::FIELD_NAME_FIRST_MESSAGE, 0);
		$this->form->append($input);
	
		return $this;
	}
	
	public function buildForm() {
		$this->form->build()
			->engageApp("chat/NewMessage");
		
		return $this;
	}
	
	public function buildLoggedInUserAsAuthor() {
		if ($this->authorId !== null) {
			return $this;
		}
		
		$this->authorId = account::getAccountID();
		$input = $this->form->getInput("hidden", self::FIELD_NAME_AUTHOR_ID, $this->authorId);
		$this->form->append($input);
		
		return $this;
	}
	
	public function buildSendButton() {
		if ($this->sendButton !== null) {
			return $this;
		}
		
		$this->sendButton = $this->form->getSubmitButton(
			appLiteral::get('chat', 'button_send_chat_message'), 
			'diomsg-send-chat-message-button'
		);
		$this->form->append($this->sendButton);
	
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
	
	public function withChat($chatId) {
		if ($this->chat !== null) {
			return $this;
		}
		
		$this->chat = $chatId;
		$input = $this->form->getInput("hidden", self::FIELD_NAME_CHAT_ID, $chatId);
		$this->form->append($input);
		
		return $this;
	}
	
	public function withMessage($message) {
		if ($this->message !== null) {
			return $this;
		}
		
		$this->message = $message;
		$input = $this->form->getTextarea(self::FIELD_NAME_MESSAGE, $message, "diomsg-message-input-field");
		$input->setAttribute("rows", 4);
		$input->setAttribute("cols", 40);
		$this->form->append($input);
		
		return $this;
	}
}
//#section_end#
?>