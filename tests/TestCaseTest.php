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
 * @version 0.1.2
 * @package Tests
 */
Namespace Serialized;

require_once(__DIR__.'/TestCase.php');

class TestCaseTest extends TestCase
{
	/**
	 * TestCase::hexDump() is currently dead code (0.0.2)
	 * but kept as it is of use for debugging.
	 */
	public function testHexDump()
	{
		$string = 'Self Test';
		$expected = '53 65 6c 66 20 54 65 73 74';
		$actual = $this->hexDump($string);
		$this->assertSame($expected, $actual);
	}
}