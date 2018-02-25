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
importer::import('UI', 'Forms', 'Form');
importer::import('AEL', 'Literals', 'appLiteral');
application::import('APP', 'Main', 'ChatMessageFormBuilder');

use UI\Forms\Form;
use APP\Main\ChatMessageFormBuilder;
use AEL\Literals\appLiteral;

class NewChatFormBuilder {

	private $form;
	
	private $ownerId;

	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	public function buildForm() {
		$this->form->build("Contact Selection")
			->engageApp("chat/New");
	
		return $this;
	}
		
	public function buildChatOwner($ownerId) {
		$this->ownerId = $ownerId;
		$ownerField = $this->form->getInput("hidden", "diomsg-owner", $ownerId);
		$this->form->append($ownerField);
		
		return $this;
	}
	
	public function buildContactList(array $contacts) {
		$contacts = $this->filterCurrentUserFromContactList($this->ownerId, $contacts);
		$options = array();
		foreach ($contacts as $contact) {
			$options[] = $this->form->getOption($contact["accountTitle"], $contact["accountID"]);
		}
		$field = $this->form->getSelect("diomsg-contact", false, "diomsg-form-field", $options);
		
		$label = $this->form->getLabel(
			appLiteral::get('chat', 'contact'),
			$field->getAttribute('id'),
			'diomsg-form-label'
		);
		
		$this->form->append($label);
		$this->form->append($field);
		
		return $this;
	}
	
	public function buildFirstMessage() {
		$field = $this->form->getTextarea(
			ChatMessageFormBuilder::FIELD_NAME_MESSAGE,
			'',
			'diomsg-message-input-field diomsg-form-field',
			false,
			true
		);
		$field->setAttribute("rows", 4);
		$field->setAttribute("cols", 40);
		
		$label = $this->form->getLabel(
			appLiteral::get('chat', 'message'),
			$field->getAttribute('id'),
			'diomsg-form-label'
		);
		
		$this->form->append($label);
		$this->form->append($field);
	
		return $this;
	}
	
	public function buildSkipCreation($skip) {
		$field = $this->form->getInput('hidden', 'diomsg-skip-creation', $skip);
		$this->form->append($field);	
	
		return $this;
	}
	
	public function buildTeamField($teamId) {
		$teamField = $this->form->getInput("hidden", "diomsg-team", $teamId);
		$this->form->append($teamField);
		
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
	
	private function filterCurrentUserFromContactList($currentUserId, array $contacts) {
		return array_values(
			array_filter(
				$contacts,
				function($contact) use ($currentUserId) {
					return $contact["accountID"] !== $currentUserId;
				}
			)
		);
	}
}
//#section_end#
?>