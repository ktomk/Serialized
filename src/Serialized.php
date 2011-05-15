<?php
/**
 * Serialized - PHP Library for Serialized Data
 *
 * Copyright (C) 2010-2011 Tom Klingenberg, some rights reserved
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program in a file called COPYING. If not, see
 * <http://www.gnu.org/licenses/> and please report back to the original
 * author.
 *
 * @author Tom Klingenberg <http://lastflood.com/>
 * @version 0.2.5
 * @package Serialized
 */

/* enable autoloading by default */
return Serialized::registerAutoload();

/**
 * Serialized autoloader
 *
 * PSR-0 compatbile autoloader in form of a collection of static class functions.
 */
class Serialized {
	/**
	 * get filename of a classname
	 *
	 * classname to filename mapping as in PSR-0
	 *
	 * @param string $className
	 * @return string filename
	 */
	public static function fileNameOfClassName($className) {
		$nameSpaceSeparator = '\\';
		$classNameSeparator = '_';
		$fileSuffix = '.php';

	    $lineup = explode($nameSpaceSeparator, $className);
	    if(!count($lineup) || !$lineup[0]) {
	    	throw new \InvalidArgumentException(sprintf('%s is not a valid classname.', $className));
	    }
	    $nonNamespacedClassName = array_pop($lineup);
	    if(!$nonNamespacedClassName) {
	    	throw new \InvalidArgumentException(sprintf('%s is not a valid classname.', $className));
	    }
	    $lineup[] = str_replace($classNameSeparator, DIRECTORY_SEPARATOR, $nonNamespacedClassName);
	    $fileName = implode(DIRECTORY_SEPARATOR, $lineup) . $fileSuffix;
	    return $fileName;
	}
	public static function autoloadCallback() {
		return array(get_called_class(), 'loadClass');
	}
	public static function registerAutoload() {
		spl_autoload_register(static::autoloadCallback());
	}
	public static function unregisterAutoload() {
		spl_autoload_unregister(static::autoloadCallback());
	}
	/**
	 * @return bool
	 */
	public static function autoloadRegistered() {
		$autoloadCallback = self::autoloadCallback();
		$autoloadFunctions = spl_autoload_functions();
		$registered = in_array($autoloadCallback, $autoloadFunctions, true)
		              || in_array(get_called_class().'::loadClass', $autoloadFunctions, true);
		return $registered;
	}
	/**
	 * @return int number of classes required()
	 */
	public static function loadLibrary() {
		$classNames = array(
			'Serialized\\ValueTypes',
			'Serialized\\TypeMap',
			'Serialized\\TypeNames',
			'Serialized\\TypeChars',
			'Serialized\\Dumper',
			'Serialized\\Dumper\\Concrete',
			'Serialized\\Dumper\\Serialized',
			'Serialized\\Dumper\\Text',
			'Serialized\\Dumper\\XML',
			'Serialized\\Value',
			'Serialized\\ParseException',
			'Serialized\\Parser',
			'Serialized\\SessionParser',
		);
		$resultArray = array_map(get_called_class().'::loadClass', $classNames);
		$resultCountable = array_map('intval', $resultArray);
		$resultCount = array_count_values($resultCountable);
		return isset($resultCount[1]) ? $resultCount[1] : 0;
	}
	/**
	 * @return bool did require(class)
	 */
	public static function loadClass($className) {
	     if (strpos($className, get_called_class().'\\') !== 0) {
            return false;
        }
		$fileName = self::fileNameOfClassName($className);
		if (self::undefined($className)) {
			require($fileName);
		}
		return true;
	}
	/**
	 * @return bool
	 */
	public static function undefined($classOrInterfaceName) {
		$classExists = class_exists($classOrInterfaceName, false);
		$interfaceExists = interface_exists($classOrInterfaceName, false);
		return (bool) !($classExists || $interfaceExists);
	}
}