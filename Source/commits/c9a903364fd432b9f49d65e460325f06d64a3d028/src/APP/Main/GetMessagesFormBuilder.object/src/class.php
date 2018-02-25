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

use \UI\Forms\Form;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 7, 2015, 22:42 (EEST)
 * @updated	September 7, 2015, 22:42 (EEST)
 */
class GetMessagesFormBuilder {

	// TODO: Move the FIELD_NAME_CHAT_ID to another class where all client classes will refer to.
	const FIELD_NAME_CHAT_ID = 'diomsg-chat-id';

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
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	public function buildChat() {
		$input = $this->form->getInput('hidden', self::FIELD_NAME_CHAT_ID);
		$this->form->append($input);
		
		return $this;
	}
	
	public function buildForm() {
		$this->form->build()
			->engageApp('chat/GetMessages');
	
		return $this;
	}
	
	public function get() {
		return $this->form;
	}
}
//#section_end#
?>