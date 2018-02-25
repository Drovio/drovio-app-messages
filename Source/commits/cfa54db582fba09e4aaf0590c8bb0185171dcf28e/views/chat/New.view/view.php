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
importer::import("UI", "Content");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Comm");
application::import("Main");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Forms\Form;
use \APP\Comm\DatabaseConnectionBuilder;
use \APP\Main\NewChatRequest;
use \APP\Main\NewChatResponse;
use \APP\Main\ChatManager;
use \APP\Main\ChatMessageFormBuilder;

$response = new APPContent();
$response->build("diomsg-chat-message-input-box-content-container", "diomsg-chat-message-input-box-content-container", true);
if (!engine::isPost()) {
  return $response->getReport();
}

$newChatRequest = NewChatRequest::fromEngine();
$chatManager = new ChatManager(new DatabaseConnectionBuilder());
$chat = $chatManager->createNewChat($newChatRequest);

$mfb = new ChatMessageFormBuilder(new Form('diomsg-new-message-form'));
$mfb->buildForm()
	->buildLoggedInUserAsAuthor()
	->withChat($chat->getId())
	->withMessage("")
	->buildSendButton();

$newChatResponse = new NewChatResponse($chat, $mfb);
$content = $newChatResponse->toArray();
$response->addReportAction("diomsg.chat.new", json_encode($content));

HTML::append($response->get(), $mfb->get()->get());

return $response->getReport("#diomsg-chat-message-input-box-container", APPContent::REPLACE_METHOD);
//#section_end#
?>