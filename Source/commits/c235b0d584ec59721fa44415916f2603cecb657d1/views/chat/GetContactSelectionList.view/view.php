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
importer::import("AEL", "Literals");
importer::import("API", "Profile");
importer::import("UI", "Apps");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Presentation\frames\dialogFrame;
use \API\Profile\account;
use \API\Profile\team;
use \AEL\Literals\appLiteral;

//$appContent = new APPContent();
//$actionFactory = $appContent->getActionFactory();
//$appContent->build("", "application_content_class", TRUE);

// Check if there is any user logged in
if (!account::validate()) {
	$appContent = new APPContent();
	$appContentElement = $appContent->build("", "diomsg-error", false)->get();
	
	$p = HTML::create("p", appLiteral::get("chat", "error_login_required"), "", "");
	HTML::append($appContentElement, $p);
	
	return $appContent->getReport("#diomsg-errors-container", APPContent::REPLACE_METHOD);
}

// Check if the current user is a member of the active team and if not return an error.
if (!team::validate()) {
	$appContent = new APPContent();
	$appContentElement = $appContent->build("", "diomsg-error", false)->get();
	
	$p = HTML::create("p", appLiteral::get("chat", "error_no_permission_in_active_team"), "", "");
	HTML::append($appContentElement, $p);
	
	return $appContent->getReport("#diomsg-errors-container", APPContent::REPLACE_METHOD);
}

// Create the "Contact Selection Dialog Frame" (CSDF).
$csdf = new dialogFrame("diomsg-contact-selection-dialog");
$csdf->build("Contact Selection")->engageApp("chat/New");

// Build the "Contacts List Form" (CLF).
//
// At first, the list of all members of the currently active team is loaded.
//
// From that list, we remove the current user. In other words, we do not allow a user to start a chat with herself.
//
// TODO: Allow multiple selection of contacts.
$currentUserId = account::getAccountID();
$contacts = team::getTeamMembers();
$clf = $csdf->getFormFactory();

$contacts = array_values(
	array_filter(
		$contacts,
		function($contact) use ($currentUserId) {
			return $contact["accountID"] !== $currentUserId;
		}
	)
);

$options = array();
foreach ($contacts as $contact) {
  $options[] = $clf->getOption($contact["accountTitle"], $contact["accountID"]);
}
$contactList = $clf->getSelect("diomsg-contact", false, "", $options);
$clf->append($contactList);

// Store the owner and her team as hidden fields to CLF.
$ownerField = $clf->getInput("hidden", "diomsg-owner", $currentUserId);
$clf->append($ownerField);

$teamField = $clf->getInput("hidden", "diomsg-team", team::getTeamID());
$clf->append($teamField);

return $csdf->getFrame();
//#section_end#
?>