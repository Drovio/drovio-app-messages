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
importer::import("UI", "Forms");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Forms\Form;
use \APP\Main\PollMessagesFormBuilder;

$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();
$appContent->build("diomsg-poll-pending-messages-view", "diomsg-poll-pending-messages-view", TRUE);

// Create the "Poll Messages Form" (pmf).
$pmfb = new PollMessagesFormBuilder(new Form('diomsg-poll-pending-messages-form'));
$pmf = $pmfb->buildForm()
	->buildChat()
	->buildParticipant()
	->get();
HTML::append($appContent->get(), $pmf->get());

return $appContent->getReport();
//#section_end#
?>