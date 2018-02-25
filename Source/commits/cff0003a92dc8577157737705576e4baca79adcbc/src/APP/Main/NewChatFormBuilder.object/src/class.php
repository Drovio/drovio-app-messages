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

importer::import('UI', 'Forms', 'Form');
importer::import('AEL', 'Literals', 'appLiteral');
application::import('APP', 'Main', 'ChatMessageFormBuilder');

use UI\Forms\Form;
use APP\Main\ChatMessageFormBuilder;
use AEL\Literals\appLiteral;

/**
 * Creates the form that allows the creation of new Chats.
 * 
 * {description}
 * 
 * @version	0.1-2
 * @created	September 29, 2015, 20:59 (EEST)
 * @updated	September 29, 2015, 21:01 (EEST)
 */
class NewChatFormBuilder {

	/**
	 * {description}
	 * 
	 * @type	Form
	 */
	private $form;
	
	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $ownerId;

	/**
	 * {description}
	 * 
	 * @param	Form	$form
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	/**
	 * Engages the form to the "chat/New" view.
	 * 
	 * @return	NewChatFormBuilder
	 * 		$this
	 */
	public function buildForm() {
		$this->form->build("Contact Selection")
			->engageApp("chat/New");
	
		return $this;
	}
		
	/**
	 * Adds a hidden field that indicates the ID of the new Chat's owner.
	 * 
	 * @param	integer	$ownerId
	 * 		{description}
	 * 
	 * @return	NewChatFormBuilder
	 * 		$this
	 */
	public function buildChatOwner($ownerId) {
		$this->ownerId = $ownerId;
		$ownerField = $this->form->getInput("hidden", "diomsg-owner", $ownerId);
		$this->form->append($ownerField);
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	array	$contacts
	 * 		The contacts available to the user for starting a new Chat.
	 * 		
	 * 		Since this array is expected to be created via the team::getTeamMembers method, it will probably contain the currently logged-in user, too. That is why this user is filtered out of the provided array. This filtering process effectively does not allow a user to start a Chat with herself.
	 * 
	 * @return	NewChatFormBuilder
	 * 		$this
	 */
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
	
	/**
	 * Adds a text input field where the user can type the first message of the Chat.
	 * 
	 * This field is required.
	 * 
	 * @return	NewChatFormBuilder
	 * 		$this
	 */
	public function buildFirstMessage() {
		$field = $this->form->getTextarea(
			ChatMessageFormBuilder::FIELD_NAME_MESSAGE,
			'',
			'diomsg-message-input-field diomsg-form-field',
			false,
			true
		);
//		$field->setAttribute("rows", 4);
//		$field->setAttribute("cols", 40);
		
		$label = $this->form->getLabel(
			appLiteral::get('chat', 'message'),
			$field->getAttribute('id'),
			'diomsg-form-label'
		);
		
		$this->form->append($label);
		$this->form->append($field);
	
		return $this;
	}
	
	/**
	 * Adds a hidden field whose value indicates whether the Chat's creation should be skipped.
	 * 
	 * @param	boolean	$skip
	 * 		{description}
	 * 
	 * @return	NewChatFormBuilder
	 * 		$this
	 * 
	 * @deprecated	The Chat is always created.
	 */
	public function buildSkipCreation($skip) {
		$field = $this->form->getInput('hidden', 'diomsg-skip-creation', $skip);
		$this->form->append($field);	
	
		return $this;
	}
	
	/**
	 * Adds a hidden field that indicates the new Chat team's ID.
	 * 
	 * @param	integer	$teamId
	 * 		{description}
	 * 
	 * @return	NewChatFormBuilder
	 * 		$this
	 */
	public function buildTeamField($teamId) {
		$teamField = $this->form->getInput("hidden", "diomsg-team", $teamId);
		$this->form->append($teamField);
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	Form
	 * 		{description}
	 */
	public function get() {
		return $this->form;
	}
	
	/**
	 * Removes the currently logged-in user from the contacts list that is made available to that user.
	 * 
	 * In other words, the currently logged-in user is not allowed to start a Chat with herself.
	 * 
	 * @param	integer	$currentUserId
	 * 		{description}
	 * 
	 * @param	array	$contacts
	 * 		See the team::getTeamMembers method for details on this array's format.
	 * 		
	 * 		This method is only interested in the "accountID" key.
	 * 
	 * @return	array
	 * 		The contacts list without the currently logged-in user.
	 */
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