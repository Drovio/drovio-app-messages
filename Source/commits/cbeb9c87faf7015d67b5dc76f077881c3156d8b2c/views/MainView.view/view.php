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
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;

$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();
$appContent->build("diomsg", "diomsg-app-content", TRUE);

$userChatListContainer = APPContent::getAppViewContainer('chat/GetUserChatList');
$sidebar = HTML::select('#diomsg-sidebar')->item(0);
HTML::append($sidebar, $userChatListContainer);

// Display the "Contact Selection Dialog Frame" (CSDF) when the user clicks on the "New Chat Button".
$newChatButton = HTML::select("#diomsg-new-chat-button")->item(0);
$actionFactory->setAction($newChatButton, "chat/GetContactSelectionList");

//// Add the container that will load the messages of the active chat.
//$gmvc = $appContent->getAppViewContainer(
//	'chat/GetMessages', 
//	array(), 
//	true, 
//	'diomsg-get-messages-view-container-placeholder', 
//	false, 
//	true
//);
//$gmvcPlaceholder = HTML::select('#diomsg-get-messages-view-container-placeholder')->item(0);
//HTML::replace($gmvcPlaceholder, $gmvc);

// Add the "Poll Pending Messages View Container" (pmvc).
$pmvc = $appContent->getAppViewContainer(
	'chat/PollPendingMessages',
	array(),
	true,
	'diomsg-poll-pending-messages-view-container',
	false,
	true
);
$pmvcPlaceholder = HTML::select('#diomsg-poll-pending-messages-view-container-placeholder')->item(0);
HTML::replace($pmvcPlaceholder, $pmvc);

return $appContent->getReport();
//#section_end#
?>