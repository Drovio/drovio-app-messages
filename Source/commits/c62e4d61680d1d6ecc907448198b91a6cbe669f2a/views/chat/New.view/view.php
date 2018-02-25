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
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Presentation\frames\dialogFrame;

$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();
$appContent->build("", "diomsg-chat", TRUE);

if (!engine::isPost()) {
  return $appContent->getReport();
}

$appContentContainer = $appContent->get();

$recipientId = engine::getVar("diomsg-contact");
$newChatNoticeElement = HTML::create("p", "Started chat with " . $recipientId . ".");
HTML::append($appContentContainer, $newChatNoticeElement);

return $appContent->getReport(
  "#diomsg-chat-list-container > .diomsg-chat-message-list-container:last-child > .diomsg-chat-initiation-message",
  APPContent::REPLACE_METHOD
);
//#section_end#
?>