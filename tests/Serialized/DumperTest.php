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

require_once(__DIR__.'/../TestCase.php');

class DumperTest extends TestCase
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

	public function testDumpOutput()
	{
		$expected = '`-- object:
     +-- classname: stdClass
     `-- members(6):
          +-- member: property
          +-- string: "test"
          +-- member: float
          +-- float: 1
          +-- member: bool
          +-- bool: TRUE
          +-- member: null
          +-- null: NULL
          +-- member: recursion
          +-- recursion: 1
          +-- member: recursionref
          `-- recursionref: &1'."\n";

		$object = new \stdClass();
		$object->property = "test";
		$object->float = (float) 1;
		$object->bool = TRUE;
		$object->null = NULL;
		$object->recursion = $object;
		$object->recursionref = &$object;
		

		$serialized = serialize($object);
		$parser = new Parser($serialized);
		$parsed = $parser->getParsed();
		$dumper = new Dumper();

		ob_start();
		$dumper->dump($parsed);
		$actual = ob_get_clean();

		$this->assertEquals($expected, $actual);
	}

	public function testUnkownValueTypeNameExceptionViaDump() {
		$parsed = array('foo', '42');
		$dumper = new Dumper();

		ob_start();
		try {
			$dumper->dump($parsed);
		} catch(\InvalidArgumentException $e) {
			ob_end_clean();
			$this->addToAssertionCount(1);
			return;
		}
		ob_end_clean();
		$this->fail('An expected exception was not thrown.');
	}
    /**
     * @expectedException \PHPUnit_Framework_Error
     */
	public function testDumpParameterException() {
		$dumper = new Dumper();
		$dumper->dump(array(), array('illegal option'));		
		return;
	}
	/**
     * @expectedException \InvalidArgumentException
     */
	public function testDumpImplParameterException() {
		$dumper = new Dumper();
		$name = 'dumpImpl';
		$class = new \ReflectionClass(get_class($dumper));
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		$method->invokeArgs($dumper, array(array(), array('invalid')));
	}
}
