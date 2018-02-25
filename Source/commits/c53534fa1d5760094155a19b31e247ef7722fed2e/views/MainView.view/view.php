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
application::import("Main");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use APP\Main\CloseChatRequest;

$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();
$appContent->build("diomsg", "diomsg-app-content", TRUE);

// Display the user "User Chat List" (UCL).
$userChatListContainer = APPContent::getAppViewContainer('chat/GetUserChatList');
$sidebar = HTML::select('#diomsg-sidebar')->item(0);
HTML::append($sidebar, $userChatListContainer);

// Display the "Contact Selection Dialog Frame" (CSDF) when the user clicks on the "New Chat Button".
$newChatButton = HTML::select("#diomsg-new-chat-button")->item(0);
$actionFactory->setAction($newChatButton, "chat/GetContactSelectionList");

// Close the currently active chat when the user clicks the "Close Chat Button".
$closeChatButton = HTML::select("#diomsg-close-chat-button")->item(0);
$actionFactory->setAction($closeChatButton, 'chat/Close', '', array(CloseChatRequest::KEY_COMPLETE_CLOSE => true));

// Add the "Delete Chat View Container" (dcvc).
$dcvc = $appContent->getAppViewContainer(
	'chat/Delete',
	array(),
	true,
	'diomsg-delete-chat-view-container',
	false,
	true
);
$dcvcPlaceholder = HTML::select('#diomsg-delete-chat-view-container-placeholder')->item(0);
HTML::replace($dcvcPlaceholder, $dcvc);

return $appContent->getReport();
//#section_end#
?>