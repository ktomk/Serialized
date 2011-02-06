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
	
	/**
	 * test dump output
	 *  
	 * @param mixed $value
	 * @return dump
	 */
	private function getDump($value) {
		$serialized = serialize($value);		
		$parser = new Parser($serialized);
		$parsed = $parser->getParsed();
		$dumper = new Dumper();
		ob_start();
		$dumper->dump($parsed);
		return ob_get_clean();
	}
	
	public function testArrayDumpOutput() {
		$expected = '`-- array(3):
     +-- [user] => string(9): "user-name"
     +-- [network] => array(1):
     |    `-- [localip] => string(7): "1.2.3.4"
     `-- [language] => string(6): "german"'."\n";

		$array = array();
		$array["user"] = "user-name";
		$array["network"] = array( "localip" => "1.2.3.4");
		$array["language"] = "german";

		$actual = $this->getDump($array);

		$this->assertEquals($expected, $actual);
	}

	public function testDumpOutput()
	{
		$expected = '`-- object(stdClass) (6):
     +-- [property] -> string(4): "test"
     +-- [float] -> float: 1
     +-- [bool] -> bool: TRUE
     +-- [null] -> null: NULL
     +-- [recursion] -> recursion: 1
     `-- [recursionref] -> recursionref: &1'."\n";

		$object = new \stdClass();
		$object->property = "test";
		$object->float = (float) 1;
		$object->bool = TRUE;
		$object->null = NULL;
		$object->recursion = $object;
		$object->recursionref = &$object;
		
		$actual = $this->getDump($object);

		$this->assertEquals($expected, $actual);
	}

	/**
     * @expectedException \InvalidArgumentException
     */
	public function testUnkownValueTypeNameExceptionViaDump() {
		$parsed = array('foo', '42');
		$dumper = new Dumper();
		$dumper->dump($parsed);
	}
    /**
     * @expectedException \PHPUnit_Framework_Error
     */
	public function testDumpParameterException() {
		$dumper = new Dumper();
		$dumper->dump(array(array(), array('illegal option')));		
		return;
	}
    /**
     * @expectedException \InvalidArgumentException
     */
	public function testDumpParameterException2() {
		$dumper = new Dumper();
		$dumper->dump(array());		
		return;
	}}
