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
 * A general purpose cache.
 * 
 * Only strings are allowed as keys.
 * 
 * @version	0.1-3
 * @created	September 18, 2015, 20:53 (EEST)
 * @updated	September 28, 2015, 17:46 (EEST)
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
	 * Same as the "add" method, except that the "key" is added to the cache only if it is not already in the cache.
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
	 * Returns the value corresponding to the "key".
	 * 
	 * @param	string	$key
	 * 		{description}
	 * 
	 * @return	mixed
	 * 		{description}
	 */
	public function get($key) {
		return $this->isPresent($key) ? $this->entries[$key] : null;
	}
	
	/**
	 * {description}
	 * 
	 * @param	string	$key
	 * 		{description}
	 * 
	 * @return	boolean
	 * 		Returns "true" if this Cache contains a value corresponding to the "key".
	 */
	public function isPresent($key) {
		return isset($this->entries[$key]);
	}
	
	/**
	 * Removes the entry corresponding to the "key".
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