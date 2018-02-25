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

use UI\Forms\Form;

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
	
		$contactList = $this->form->getSelect("diomsg-contact", false, "", $options);
		$this->form->append($contactList);
		
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