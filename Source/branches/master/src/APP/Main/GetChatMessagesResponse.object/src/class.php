<?php
//#section#[header]
// Namespace
namespace APP\Main;

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
 * @library	APP
 * @package	Main
 * 
 * @copyright	Copyright (C) 2015 Messages. All rights reserved.
 */

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	September 29, 2015, 20:43 (EEST)
 * @updated	September 29, 2015, 20:43 (EEST)
 * 
 * @deprecated	A polling mechanism is used in place of simple GET requests. See the PollMessagesResponse class.
 */
class GetChatMessagesResponse
{
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		// Put your constructor method code here.
	}
}
//#section_end#
?>