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
importer::import("API", "Comm", "database/dbConnection");

use API\Comm\database\dbConnection;

class DatabaseConnectionBuilder
{
	/**
	 * @var dbConnection
	 */
	private $connection;

	/**
	 * @var string
	 */
	private $database = 'drovio.messages';

	/**
	 * @var string
	 */
	private $dbType = 'MySQL';

	/**
	 * @var string
	 */
	private $host = 'localhost';
	
	/**
	 * @param string
	 */
	private $password = 'a26bBU22782ex2ju';
	
	/**
	 * @param string
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