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
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use APP\Main\GetMessagesFormBuilder;
use UI\Forms\Form;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\ChatManager;
use APP\Main\GetChatMessagesRequest;
use APP\Main\GetChatMessagesResponseBuilder;
use API\Profile\account;
use APP\Main\OpenChatResponse;
use APP\Main\ChatMessageFormBuilder;

$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();
$appContent->build("diomsg-chat-message-input-box-content-container", "diomsg-chat-message-input-box-content-container", true);

if (!engine::isPost()) {
	return $appContent->getReport("#diomsg-chat-message-input-box-container", APPContent::REPLACE_METHOD);
}

// Load the chat messages.
$request = GetChatMessagesRequest::fromEngine();
$chatId = $request->getChatId();
$userAccountId = account::getAccountID();

$chatManager = new ChatManager(new DatabaseConnectionBuilder());
$messages = $chatManager->findChatMessages($chatId, $userAccountId);

$responseBuilder = new OpenChatResponse($userAccountId);
$responseArray = $responseBuilder->buildChat($chatId)
	->buildMessages($messages)
	->toArray();

$appContent->addReportAction('diomsg.chat.opened', json_encode($responseArray));

// Add the "Message Form".
$mfb = new ChatMessageFormBuilder(new Form('diomsg-new-message-form'));
$mfb->buildForm()
	->buildLoggedInUserAsAuthor()
	->withChat($chatId)
	->withMessage("")
	->buildSendButton();
HTML::append($appContent->get(), $mfb->get()->get());

return $appContent->getReport("#diomsg-chat-message-input-box-container", APPContent::REPLACE_METHOD);
//#section_end#
?>