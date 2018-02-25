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
application::import('Main', 'ChatMessageFormBuilder');

use \APP\Main\ChatMessageFormBuilder;
use \UI\Forms\Form;

class DeleteChatFormBuilder {
 
	// TODO: Move the FIELD_NAME_CHAT_ID to another class where all client classes will refer to.
 	const FIELD_NAME_CHAT_ID = 'diomsg-chat-id';

	const FIELD_NAME_DELETE_IF_EMPTY = 'diomsg-delete-if-empty';

	private $form;

	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	public function buildForm() {
		$this->form->build()
			->engageApp('chat/Delete');
			
		return $this;
	}
	
	public function buildChatField() {
		$field = $this->form->getInput('hidden', self::FIELD_NAME_CHAT_ID, '-1');
		$this->form->append($field);
	
		return $this;
	}
	
	public function buildDeleteChatIfEmptyField($value) {
		$field = $this->form->getInput('hidden', self::FIELD_NAME_DELETE_IF_EMPTY, $value);
		$this->form->append($field);
		
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
}
//#section_end#
?>