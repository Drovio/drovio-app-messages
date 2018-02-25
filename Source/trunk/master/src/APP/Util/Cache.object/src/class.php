<?php
//#section#[header]
// Namespace
namespace APP\Util;

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
 * @package	Util
 * 
 * @copyright	Copyright (C) 2015 Messages. All rights reserved.
 */

/**
 * {title}
 * 
 * {description}
 * 
 * @version	1.0-1
 * @created	September 10, 2015, 14:01 (EEST)
 * @updated	September 10, 2015, 14:03 (EEST)
 */
class Cache {

	/**
	 * {description}
	 * 
	 * @type	array
	 */
	private $entries;

	/**
	 * {description}
	 * 
	 * @return	void
	 */
	public function __construct() {
		$this->entries = array();
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$key
	 * 		{description}
	 * 
	 * @param	mixed	$value
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function add($key, $value) {
		$this->entries[$key] = $value;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$key
	 * 		{description}
	 * 
	 * @param	mixed	$value
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function addIfNotPresent($key, $value) {
		if ($this->isPresent($key)) {
			return;
		}
		
		$this->add($key, $value);
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$key
	 * 		{description}
	 * 
	 * @return	mixed
	 * 		The value corresponding to $key or "null" if no such value exists.
	 */
	public function get($key) {
		return $this->isPresent($key) ? $this->entries[$key] : null;
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$key
	 * 		{description}
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	public function isPresent($key) {
		return isset($this->entries[$key]);
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$key
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function remove($key) {
		unset($this->entries[$key]);
	}
}
//#section_end#
?>