<?php
/**
 * Serialized - PHP Library for Serialized Data
 *
 * Copyright (C) 2010  Tom Klingenberg
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
 * @version 0.1.0
 * @package Tests
 */

Namespace Serialized;

require_once(__DIR__.'/../src/Serialized.php');

require_once('PHPUnit/Autoload.php');

/**
 * abstract, base test-case class.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{	
	/**
	 * assert the last error
	 * 
	 * @param string $message of error or notice
	 * @param string $file where to expect that message
	 */
	protected function assertLastError($message, $file)
	{
		$lastError = error_get_last();
		$condition = NULL === $lastError;
		$this->assertFalse($condition);
		
		extract($lastError, EXTR_PREFIX_ALL, 'last');
		$this->assertEquals($message, $last_message);
		$this->assertEquals(basename($file), basename($last_file));
	}
	/**
	 * hexdump of string
	 *  
	 * @param string $string
	 * @return string hexdump
	 */
	protected function hexDump($string) {
		$return = '';
		$len = strlen($string);
		for($i=0; $i<$len; $i++) {
			$char = $string[$i];
			$point = substr('0'.dechex(ord($char)), -2);
			$i && $return .= ' ';
			$return .= $point;
		}
		return $return;
	}
}