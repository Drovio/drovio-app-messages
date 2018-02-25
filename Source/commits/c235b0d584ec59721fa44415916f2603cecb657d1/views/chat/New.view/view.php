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
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Comm");
application::import("Main");
//#section_end#
//#section#[view]
use \UI\Content\JSONContent;
use \APP\Comm\DatabaseConnectionBuilder;
use \APP\Main\NewChatRequest;
use \APP\Main\NewChatResponse;
use \APP\Main\ChatManager;

$response = new JSONContent();
if (!engine::isPost()) {
  return $response->getReport();
}

$newChatRequest = NewChatRequest::fromEngine();
$chatManager = new ChatManager(new DatabaseConnectionBuilder());
$chat = $chatManager->createNewChat($newChatRequest);

$newChatResponse = new NewChatResponse($chat);
$response->addReportAction("diomsg.chat.new", json_encode($newChatResponse->toArray()));

return $response->getReport($content);
//#section_end#
?>