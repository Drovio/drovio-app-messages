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
 * @library	APP
 * @package	Comm
 * 
 * @copyright	Copyright (C) 2015 Messages. All rights reserved.
 */

importer::import("API", "Comm", "database/dbConnection");

use API\Comm\database\dbConnection;

/**
 * {title}
 * 
 * Creates a dbConnection.
 * 
 * @version	0.1-1
 * @created	September 18, 2015, 20:49 (EEST)
 * @updated	September 18, 2015, 20:49 (EEST)
 */
class DatabaseConnectionBuilder {

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $connection;

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $database = 'drovio.messages';

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $dbType = 'MySQL';

	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $host = 'localhost';
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $password = 'a26bBU22782ex2ju';
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $username = 'drvmsg';
	
	function getConnection() {
		if ($this->connection !== null) {
			return $this->connection;
		}
			
		return $this->connection = new dbConnection(
			$this->dbType,
			$this->host,
			$this->database,
			$this->username,
			$this->password
		);
	}
}
//#section_end#
?>