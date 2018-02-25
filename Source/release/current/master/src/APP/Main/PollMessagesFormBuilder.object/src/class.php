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
application::import('Main', 'GetMessagesFormBuilder');

use UI\Forms\Form;
use APP\Main\GetMessagesFormBuilder;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 22:42 (EEST)
 * @updated	September 29, 2015, 22:42 (EEST)
 * 
 * @deprecated	No form is needed anymore since the GET HTTP method is used for polling.
 */
class PollMessagesFormBuilder {

 	/**
 	 * {description}
 	 * 
 	 * @type	{type}
 	 */
 	const FIELD_NAME_PARTICIPANT_ID = 'diomsg-participant-id';

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $form;

	/**
	 * {description}
	 * 
	 * @param	{type}	$form
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct(Form $form) {
		$this->form = $form;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function buildForm() {
		$this->form->build()
			->engageApp('chat/PollPendingMessages');
			
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function buildChat() {
		$input = $this->form->getInput('hidden', GetMessagesFormBuilder::FIELD_NAME_CHAT_ID);
		$this->form->append($input);
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$participantId
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function buildParticipant($participantId) {
		$input = $this->form->getInput('hidden', self::FIELD_NAME_PARTICIPANT_ID, $participantId);
		$this->form->append($input);
		
		return $this;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function get() {
		return $this->form;
	}
}
//#section_end#
?>