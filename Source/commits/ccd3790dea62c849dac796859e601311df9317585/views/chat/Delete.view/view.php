<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 61;

// Init Application and Application literal
application::init(61);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use APP\Main\DeleteChatFormBuilder;
use UI\Forms\Form;
use APP\Main\DeleteChatRequest;
use APP\Main\CloseChatRequest;

$appContent = new APPContent();
$appContent->build('diomsg-delete-chat-view', 'diomsg-delete-chat-view', TRUE);

//// Create the "Delete Chat Form" (dcf);
//$dcfb = new DeleteChatFormBuilder(new Form('diomsg-delete-chat-form'));
//$dcfb->buildForm()
//	->buildChatField()
//	->buildDeleteChatIfEmptyField('-1');
//HTML::append($appContent->get(), $dcfb->get()->get());
//
//if (!engine::isPost()) {
//	return $appContent->getReport('#diomsg-delete-chat-view-container', APPContent::REPLACE_METHOD);
//}

// Read the request data.
$dcr = DeleteChatRequest::fromEngine();
//throw new \Exception(var_dump($dcr));

// Close the chat to be deleted if it is open.
$ccvc = $appContent->getAppViewContainer(
	'chat/Close',
	array(
		CloseChatRequest::KEY_COMPLETE_CLOSE => 1,
		CloseChatRequest::FIELD_NAME_CHAT_ID => $dcr->getChatId()
	),
	true,
	'',
	false,
	false
);
HTML::append($appContent->get(), $ccvc);

return $appContent->getReport('#diomsg-empty-response-container', APPContent::REPLACE_METHOD);
//#section_end#
?>