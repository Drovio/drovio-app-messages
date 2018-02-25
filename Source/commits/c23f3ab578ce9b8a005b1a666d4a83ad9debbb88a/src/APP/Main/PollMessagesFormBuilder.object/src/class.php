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
application::import('Main', 'GetMessagesFormBuilder');

use UI\Forms\Form;
use APP\Main\GetMessagesFormBuilder;

class PollMessagesFormBuilder {

	// TODO: Move the FIELD_NAME_PARTICIPANT_ID to another class where all client classes will refer to.
 	const FIELD_NAME_PARTICIPANT_ID = 'diomsg-participant-id';

	private $form;

	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	public function buildForm() {
		$this->form->build()
			->engageApp('chat/PollPendingMessages');
			
		return $this;
	}
	
	public function buildChat() {
		$input = $this->form->getInput('hidden', GetMessagesFormBuilder::FIELD_NAME_CHAT_ID);
		$this->form->append($input);
		
		return $this;
	}
	
	public function buildParticipant($participantId) {
		$input = $this->form->getInput('hidden', self::FIELD_NAME_PARTICIPANT_ID, $participantId);
		$this->form->append($input);
		
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
}
//#section_end#
?>