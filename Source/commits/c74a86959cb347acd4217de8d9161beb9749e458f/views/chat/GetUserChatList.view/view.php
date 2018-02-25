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
use \UI\Apps\APPContent;
use \APP\Comm\DatabaseConnectionBuilder;
use \APP\Main\ChatManager;
use \API\Profile\account;
use \APP\Main\UserChatListFormBuilder;
use \UI\Forms\Form;

$appContent = new APPContent();
$appContent->build('diomsg-chat-list-container', 'diomsg-chat-list-container', true);
$actionFactory = $appContent->getActionFactory();

$chatManager = new ChatManager(new DatabaseConnectionBuilder());
$userAccountId = account::getAccountID();
$userChatList = $chatManager->findLastChatsOfParticipant($userAccountId, 10);

$userChatListFormBuilder = new UserChatListFormBuilder();
$userChatListFormBuilder->buildForm(new Form())
	->buildSelectedChatField()
	->buildList();
foreach ($userChatList as $chat) {
	$userChatListFormBuilder->buildItem($chat->getId(), $chat->getLastMessageAt()->format(ChatManager::DATE_TIME_FORMAT));
}

$userChatListContainer = $userChatListFormBuilder->buildContainer()->get();
HTML::append($appContent->get(), $userChatListContainer);

return $appContent->getReport('#diomsg-sidebar', APPContent::APPEND_METHOD);
//#section_end#
?>