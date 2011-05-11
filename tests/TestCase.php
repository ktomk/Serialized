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
 * @version 0.2.1
 * @package Tests
 */

Namespace Serialized;

require_once(__DIR__.'/../src/Serialized.php');

require_once('PHPUnit/Autoload.php');

require_once(__DIR__.'/Constraints.php');

/**
 * abstract, base test-case class.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * assert lint of a php file
	 */
	protected function assertLint($filename, $message = '') {
		self::assertThat($filename, new ConstraintLint, $message);
	}
	/**
	 * assert the last error
	 */
	protected function assertLastError($error, $file, $message = '') {
		self::assertThat($file, new ConstraintLastError($error), $message);
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