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
importer::import("UI", "Content");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Comm");
application::import("Main");
application::import("Persist");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use UI\Forms\Form;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\NewChatRequest;
use APP\Main\NewChatResponse;
use APP\Main\ChatManager;
use APP\Main\ChatMessageFormBuilder;
use APP\Main\PollMessagesFormBuilder;
use APP\Main\GetMessagesFormBuilder;
use API\Profile\account;
use APP\Persist\IdGenerator;

$response = new APPContent();
$response->build("diomsg-chat-message-input-box-content-container", "diomsg-chat-message-input-box-content-container", true);
if (!engine::isPost()) {
  return $response->getReport();
}

// Close any currently active chats.
$ccvc = $response->getAppViewContainer(
	'chat/Close',
	array(),
	false,
	'',
	false,
	true
);
HTML::append($response->get(), $ccvc);

// Save the new chat.
$newChatRequest = NewChatRequest::fromEngine();
$chatManager = new ChatManager(new DatabaseConnectionBuilder(), new IdGenerator());
$chat = $chatManager->createNewChat($newChatRequest);

// Create the first message.
$authorId = $newChatRequest->getOwnerId();
$nmvc = $response->getAppViewContainer(
	'chat/NewMessage',
	array(
		ChatMessageFormBuilder::FIELD_NAME_CHAT_ID => $chat->getId(),
		ChatMessageFormBuilder::FIELD_NAME_AUTHOR_ID => $authorId,
		ChatMessageFormBuilder::FIELD_NAME_MESSAGE => $newChatRequest->getFirstMessageContent(),
		ChatMessageFormBuilder::FIELD_NAME_FIRST_MESSAGE => 1
	),
	true,
	'',
	false,
	false
);
HTML::append($response->get(), $nmvc);

// Create the "New Message Form" (nmf).
$mfb = new ChatMessageFormBuilder(new Form('diomsg-new-message-form'));
$mfb->buildForm()
	->buildLoggedInUserAsAuthor()
	->withChat($chat->getId())
	->buildFirstMessage()
	->withMessage("")
	->buildSendButton();

HTML::append($response->get(), $mfb->get()->get());

// Notify for the saving of a new chat.
$newChatResponse = new NewChatResponse($chat, $mfb);
$content = $newChatResponse->toArray();
$response->addReportAction("diomsg.chat.new", json_encode($content));

// Set the chat messages polling action.
$pmvc = $response->getAppViewContainer(
	'chat/PollPendingMessages',
	array(
		GetMessagesFormBuilder::FIELD_NAME_CHAT_ID => $chat->getId(),
		PollMessagesFormBuilder::FIELD_NAME_PARTICIPANT_ID => $authorId	
	),
	true,
	'diomsg-poll-pending-messages-view-container',
	false
);
$pmvcPlaceholder = HTML::select('#diomsg-poll-pending-messages-view-container-placeholder')->item(0);
HTML::replace($pmvcPlaceholder, $pmvc);

return $response->getReport("#diomsg-chat-message-input-box-container", APPContent::REPLACE_METHOD);
//#section_end#
?>