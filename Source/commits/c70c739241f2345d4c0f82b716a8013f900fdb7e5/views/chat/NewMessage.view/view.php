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

// Import APP Packages
application::import("Comm");
application::import("Main");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use APP\Main\NewChatMessageRequest;
//use APP\Main\ChatMessageManager;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\ChatManager;
use APP\Main\NewChatMessageResponse;

$appContent = new APPContent();
$appContent->build("", "diomsg-chat-message-list-item", TRUE);
//if (!engine::isPost()) {
//  return $response->getReport("#diomsg-chat-list-container .diomsg-chat-message-list");
//}

// Store the new message to the database.
$newMessageRequest = NewChatMessageRequest::fromEngine();
$messageManager = new ChatManager(new DatabaseConnectionBuilder());
$message = $messageManager->createNewMessage($newMessageRequest);

// Mark the new message as a pending message for the chat recipients.
$messageManager->createNewPendingMessage($message->getId(), $newMessageRequest->getChatId());

// Create message copies for each chat participant.
$messageManager->createMessageCopies($message);

$newMessageResponse = new NewChatMessageResponse();
$newMessageResponseArray = $newMessageResponse->buildChatId($newMessageRequest->getChatId())
	->buildFirstMessage($newMessageRequest->isFirstMessage())
	->toArray();

$appContent->addReportAction("diomsg.message.new", json_encode($newMessageResponseArray));

return $appContent->getReport("#diomsg-chat-list-container .diomsg-chat-message-list", APPContent::APPEND_METHOD);
//#section_end#
?>