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
use APP\Comm\DatabaseConnectionBuilder;
use APP\Main\ChatManager;
use API\Profile\account;
use APP\Main\UserChatListFormBuilder;
use UI\Forms\Form;
use API\Profile\team;
use APP\Main\ChatMessageFormBuilder;

$appContent = new APPContent();
$appContent->build('diomsg-user-chat-list-view-container', 'diomsg-user-chat-list-view-container', true);
$actionFactory = $appContent->getActionFactory();

$chatManager = new ChatManager(new DatabaseConnectionBuilder());
$userAccountId = account::getAccountID();
$teamId = team::getTeamID();
$userChatList = $chatManager->findLastChatsOfParticipantForTeam($teamId, $userAccountId);

$uclfb = new UserChatListFormBuilder($chatManager, $userAccountId, $actionFactory);
$uclfb->buildList();
foreach ($userChatList as $chatListItem) {
	$uclfb->buildItem($chatListItem);
}

$uclc = $uclfb->buildContainer()->get();
HTML::append($appContent->get(), $uclc);

return $appContent->getReport('#diomsg-sidebar', APPContent::REPLACE_METHOD);
//#section_end#
?>