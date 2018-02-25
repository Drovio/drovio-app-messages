<?php
//#section#[header]
// Namespace
namespace APP\Comm;

require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import application loader
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;
//#section_end#
//#section#[class]
/**
 * @deprecated
 */
class dbConnection
{
	// Constructor Method
	public function __construct()
	{
		// Put your constructor method code here.
	}
}
//#section_end#
?>