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
importer::import("API", "Profile");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
application::import("Comm");
application::import("Main");
application::import("Persist");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use APP\Main\DeleteChatFormBuilder;
use UI\Forms\Form;
use APP\Main\DeleteChatRequest;
use APP\Main\CloseChatRequest;
use APP\Main\DeleteChatResponse;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\ChatManager;
use API\Profile\account;
use APP\Persist\IdGenerator;

$appContent = new APPContent();
$appContent->build('diomsg-delete-chat-view', 'diomsg-delete-chat-view', TRUE);

// Read the request data.
$dcr = DeleteChatRequest::fromEngine(); 

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

/* Delete the chat.
 *
 * This is a two-step process:
 *  - Delete all the chat messages' copies that were made for the current user.
 *  - If no other participant exists for this chat, delete it from the database entirely.
 */
$chatId = $dcr->getChatId();
$currentUserId = account::getAccountID();
$cm = new ChatManager(new DatabaseConnectionBuilder(), new IdGenerator());
$cm->deleteChatForParticipant($chatId, $currentUserId);
$chatParticipants = $cm->findChatParticipants($chatId, 0);
if (empty($chatParticipants)) {
	$cm->deleteChat($chatId);
}

// Notify of the chat deletion.
$response = new DeleteChatResponse($dcr->getChatId());

$appContent->addReportAction('diomsg.chat.deleted', json_encode($response->toArray()));

return $appContent->getReport('#diomsg-empty-response-container', APPContent::REPLACE_METHOD);
//#section_end#
?>