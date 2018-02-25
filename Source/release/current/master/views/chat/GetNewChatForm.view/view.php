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
application::import("Main");
//#section_end#
//#section#[view]
use UI\Apps\APPContent;
use UI\Forms\Form;
use APP\Main\NewChatFormBuilder;
use API\Profile\account;
use API\Profile\team;

$appContent = new APPContent();
$appContent->build('diomsg-new-chat-form-view', 'diomsg-new-chat-form-view', true);

// Create the "New Chat Form" (ncf).
$currentUserId = account::getAccountID();
$contacts = team::getTeamMembers();

$ncfb = new NewChatFormBuilder(new Form('diomsg-new-chat-form'));
$ncfb->buildForm()
	->buildChatOwner($currentUserId)
	->buildContactList($contacts)
	->buildSkipCreation('false')
	->buildTeamField(team::getTeamID());
HTML::append($appContent->get(), $ncfb->get()->get());

return $appContent->getReport('#diomsg-new-chat-form-view-container', APPContent::REPLACE_METHOD);
//#section_end#
?>