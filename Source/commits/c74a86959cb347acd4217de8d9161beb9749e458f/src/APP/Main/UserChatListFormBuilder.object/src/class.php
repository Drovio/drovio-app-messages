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
importer::import('UI', 'Html', 'HTML');

use \UI\Forms\Form;
use \UI\Html\HTML;

class UserChatListFormBuilder {

	private $container;

	private $list;

	private $form;
	
	public function __construct() {
		$this->items = array();
	}
	
	public function buildContainer() {
		$this->container = HTML::div('', 'diomsg-user-chat-list-container');
		HTML::append($this->container, $this->form->get());
		HTML::append($this->container, $this->list);
		
		return $this;
	}
	
	public function buildForm(Form $form) {
		$this->form = $form;
		
		$this->form->build()
			->engageApp('chat/Open');
			
		return $this;
	}
	
	public function buildItem($chatId, $lastMessageTime) {
		$item = HTML::li('Last message at: ' . $lastMessageTime, 'diomsg-user-chat-' . $chatId, 'diomsg-user-chat');
		HTML::append($this->list, $item);
		
		return $this;
	}
	
	public function buildList() {
		$this->list = HTML::ul('', 'diomsg-user-chat-list');
		
		return $this;
	}
	
	public function buildSelectedChatField() {
		$field = $this->form->getInput('hidden', 'diomsg-user-chat-list-selected-chat');
		$this->form->append($field);
	
		return $this;
	}
	
	public function get() {
		return $this->container;
	}
}
//#section_end#
?>