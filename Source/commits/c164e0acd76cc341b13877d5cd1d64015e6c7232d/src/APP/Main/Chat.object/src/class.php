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

application::import('Main', 'ChatParticipant');
application::import('Main', 'ChatParticipantTeam');

use \APP\Main\ChatParticipant;
use \APP\Main\ChatParticipantTeam;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	3.0-1
 * @created	August 29, 2015, 12:13 (EEST)
 * @updated	August 31, 2015, 17:31 (EEST)
 */
class Chat {

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $id;

	/**
	 * {description}
	 * 
	 * @type	ChatParticipant
	 */
	private $owner;
	
	/**
	 * {description}
	 * 
	 * @type	ChatParticipant[]
	 */
	private $recipients;
	
	/**
	 * {description}
	 * 
	 * @type	ChatParticipantTeam
	 */
	private $team;

	/**
	 * {description}
	 * 
	 * @param	ChatParticipant	$owner
	 * 		{description}
	 * 
	 * @param	ChatParticipant[]	$recipients
	 * 		{description}
	 * 
	 * @param	ChatParticipantTeam	$team
	 * 		{description}
	 * 
	 * @return	void
	 * 
	 * @throws	\InvalidArgumentException
	 */
	public function __construct(ChatParticipant $owner, array $recipients, ChatParticipantTeam $team) {
		if (!$this->isValid($owner, $recipients, $team)) {
			throw new \InvalidArgumentException('The owner and the recipients must all be members of the provided team.');
		}
		
		$this->owner = $owner;
		$this->recipients = $recipients;
		$this->team = $team;
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
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function getRecipients() {
		return $this->recipients;
	}
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function getTeam() {
		return $this->team;
	}
	
	/**
	 * Sets the id of this Chat only the first time it is called.
	 * 
	 * Any further calls have no effect on this Chat instance.
	 * 
	 * This method is useful for setting this Chat's id right after the instance has been saved to the database and an id has been issued.
	 * 
	 * @param	integer	$id
	 * 		{description}
	 * 
	 * @return	void
	 * 
	 * @throws	\InvalidArgumentException
	 */
	public function initId($id) {
		if ($this->id !== null) {
			return;
		}
		
		$id = intval($id);
		if ($id <= 0) {
			throw new \InvalidArgumentException('The id cannot be an integer smaller than 1.');
		}
		
		$this->id = $id;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$owner
	 * 		{description}
	 * 
	 * @param	{type}	$recipients
	 * 		{description}
	 * 
	 * @param	{type}	$team
	 * 		{description}
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	private function isValid(ChatParticipant $owner, array $recipients, ChatParticipantTeam $team) {
		if (!$owner->isMemberOf($team)) {
			return false;
		}
		
		foreach ($recipients as $recipient) {
			if (!$recipient->isMemberOf($team)) {
				return false;
			}
		}
		
		return true;
	}
}
//#section_end#
?>