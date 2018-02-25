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

// Import APP Packages
application::import("Comm");
application::import("Main");
application::import("Persist");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use APP\Main\CloseChatRequest;
use APP\Main\CloseChatResponse;
use API\Profile\account;
use APP\Main\ChatManager;
use APP\Comm\DatabaseConnectionBuilder;
use APP\Persist\IdGenerator;

$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();
$appContent->build('', '', false);

$request = CloseChatRequest::fromEngine();
$userAccountId = account::getAccountID();
$chatId = $request->getChatId();

$cm = new ChatManager(new DatabaseConnectionBuilder(), new IdGenerator());
$cm->deactivateAllChatsOfParticipant($userAccountId);

$response = new CloseChatResponse($request->isCompleteClose(), $chatId);
$responseArray = $response->toArray();

$appContent->addReportAction('diomsg.chat.closed', json_encode($responseArray));

return $appContent->getReport('#diomsg-empty-response-container', APPContent::REPLACE_METHOD);
//#section_end#
?>