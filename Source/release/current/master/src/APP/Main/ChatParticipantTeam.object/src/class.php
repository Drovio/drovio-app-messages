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
 * Represents a team a ChatParticipant is member of.
 * 
 * See the "Setter Methods Naming Convention" note in the Chat class description.
 * 
 * @version	1.0-2
 * @created	August 29, 2015, 12:04 (EEST)
 * @updated	September 29, 2015, 20:24 (EEST)
 */
class ChatParticipantTeam {

	/**
	 * The team's ID.
	 * 
	 * @type	integer
	 */
	private $id;
	
	/**
	 * Creates a new ChatParticipantTeam were only its ID is set.
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipantTeam
	 * 		{description}
	 */
	public static function newWithId($id) {
		$team = new ChatParticipantTeam();
		$team->withId($id);
		
		return $team;
	}
	
	/**
	 * {description}
	 * 
	 * @return	integer
	 * 		This team's ID.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * {description}
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	ChatParticipantTeam
	 * 		{description}
	 */
	public function withId($id) {
		if ($this->id === null) {
			$this->id = $id;
		}
		
		return $this;
	}
}
//#section_end#
?>