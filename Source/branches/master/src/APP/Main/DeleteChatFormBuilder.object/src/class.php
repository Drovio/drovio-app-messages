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

importer::import("UI", "Forms", "Form");
application::import('Main', 'ChatMessageFormBuilder');

use \APP\Main\ChatMessageFormBuilder;
use \UI\Forms\Form;

/**
 * Creates the form that allows the deletion of a Chat.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:36 (EEST)
 * @updated	September 29, 2015, 20:36 (EEST)
 */
class DeleteChatFormBuilder {
 
 	/**
 	 * {description}
 	 * 
 	 * @type	string
 	 */
 	const FIELD_NAME_CHAT_ID = 'diomsg-chat-id';

	/**
	 * {description}
	 * 
	 * @type	string
	 */
	const FIELD_NAME_DELETE_IF_EMPTY = 'diomsg-delete-if-empty';

	/**
	 * {description}
	 * 
	 * @type	Form
	 */
	private $form;

	/**
	 * {description}
	 * 
	 * @param	Form	$form
	 * 		The Form that is being built by this builder.
	 * 
	 * @return	void
	 */
	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	/**
	 * Engages the form to the "chat/Delete" view.
	 * 
	 * @return	DeleteChatFormBuilder
	 * 		$this
	 */
	public function buildForm() {
		$this->form->build()
			->engageApp('chat/Delete');
			
		return $this;
	}
	
	/**
	 * Adds a hidden field that contains the Chat's ID.
	 * 
	 * @return	DeleteChatFormBuilder
	 * 		$this
	 */
	public function buildChatField() {
		$field = $this->form->getInput('hidden', self::FIELD_NAME_CHAT_ID, '-1');
		$this->form->append($field);
	
		return $this;
	}
	
	/**
	 * Adds a hidden field that indicates if the Chat should be deleted only if it is empty.
	 * 
	 * A Chat is empty if it contains no messages.
	 * 
	 * @param	boolean	$value
	 * 		A value of "true" indicates that the Chat should be deleted only if it is empty.
	 * 
	 * @return	DeleteChatFormBuilder
	 * 		$this
	 */
	public function buildDeleteChatIfEmptyField($value) {
		$field = $this->form->getInput('hidden', self::FIELD_NAME_DELETE_IF_EMPTY, $value);
		$this->form->append($field);
		
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
}
//#section_end#
?>