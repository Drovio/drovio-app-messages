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
		$field = $this->form->getInput('hidden', ChatMessageFormBuilder::FIELD_NAME_CHAT_ID, '');
		$this->form->append($field);
	
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
}
//#section_end#
?>