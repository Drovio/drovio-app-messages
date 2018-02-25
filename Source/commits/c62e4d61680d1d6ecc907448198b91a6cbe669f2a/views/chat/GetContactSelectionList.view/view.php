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
//use \UI\Apps\APPContent;
use \UI\Presentation\frames\dialogFrame;

//$appContent = new APPContent();
//$actionFactory = $appContent->getActionFactory();
//$appContent->build("", "application_content_class", TRUE);

// Create the "Contact Selection Dialog Frame" (CSDF).
$csdf = new dialogFrame("diomsg-contact-selection-dialog");
$csdf->build("Contact Selection")->engageApp("chat/New");

// TODO: Load the contacts from the database.
$contacts = array(
  "user-id" => array(
    "id" => "user-id", 
    "name" => "Me"
  )
);

// Build the "Contacts List Form" (CLF).
//
// TODO: Allow multiple selection of contacts.
$clf = $csdf->getFormFactory();

$options = array();
foreach ($contacts as $contact) {
  $options[] = $clf->getOption($contact["name"], $contact["id"]);
}
$contactList = $clf->getSelect("diomsg-contact", false, "", $options);
$clf->append($contactList);

return $csdf->getFrame();
//#section_end#
?>