<?php
//#section#[header]
// Namespace
namespace APP\Persist;

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
application::import('Persist', 'IdGeneratorInterface');

use APP\Persist\IdGeneratorInterface;

class IdGenerator implements IdGeneratorInterface {


	function generate(array $parts) {
		$now = time();
		$randomNumber = mt_rand();
		
		$parts = array_merge($parts, array($now, $randomNumber));
		
		return implode('_', $parts);
	}
}
//#section_end#
?>