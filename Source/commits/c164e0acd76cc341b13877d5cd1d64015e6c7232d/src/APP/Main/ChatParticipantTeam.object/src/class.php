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
 * @created	August 29, 2015, 12:04 (EEST)
 * @updated	August 29, 2015, 12:04 (EEST)
 */
class ChatParticipantTeam {

	/**
	 * {description}
	 * 
	 * @type	integer
	 */
	private $id;

	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function __construct($id) {
		$this->id = $id;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		{description}
	 */
	public function getId() {
		return $this->id;
	}
}
//#section_end#
?>