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
 * @version 0.1.6
 * @package Tests
 */

Namespace Serialized;

require_once(__DIR__.'/../TestCase.php');

/**
 * Test the config (sub-) functionality of the dumper base
 * class (which is a private function).
 */
class DumperConfigTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	private function callMerge($args) {
		$selfObject = Dumper::factory('Text');
		$object = new \ReflectionObject($selfObject);
		$method = $object->getMethod('configMergeDeep');
		$method->setAccessible(true);

		return $method->invokeArgs($selfObject, $args);
	}

	public function testConfigMerge()
	{
		# drop numeric keys
		$args = array(array(), array(0=>1));
		$expected = array();
		$actual = $this->callMerge($args);
		$this->assertSame($expected, $actual);

		# provoke merging of values
		$args = array(array('set' => array('set'=>0)), array('set'=> array(0=>1, 1=>2, 'set'=>3)));
		$expected = array('set' => array('set'=>3));
		$actual = $this->callMerge($args);
		$this->assertSame($expected, $actual);

		# provoke undefined error
		$args = array(array(), array('undefined'=>1));
		$result = @$this->callMerge($args);
		$this->assertLastError('Configuration "/undefined" was not defined.', $file = 'Dumper.php');

		# provoke array onto non-array setting
		$args = array(array('set'=>1), array('set'=>array()));
		$result = @$this->callMerge($args);
		$this->assertLastError('Can not merge array (key: "set") into a non-array config entry.', $file = 'Dumper.php');

		# provoke non-array onto array setting
		$args = array(array('set'=>array('set'>=1)), array('set'=>1));
		$result = @$this->callMerge($args);
		$this->assertLastError('Can not overwrite existing array (key: "set") with a value ("1").', $file = 'Dumper.php');
	}
}