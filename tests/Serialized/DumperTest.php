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

require_once(__DIR__.'/../TestCase.php');

abstract class DumperTest extends TestCase
{
	protected $dumper = '';

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
	 * @param string $serialized
	 * @return dump
	 */
	protected function getDump($serialized, $parser = null) {
		$parser || $parser = 'Parser';
		$parserClass = __NAMESPACE__."\\{$parser}";
		$parser = new $parserClass($serialized);
		$parsed = $parser->getParsed();
		$class = __NAMESPACE__."\\Dumper\\{$this->dumper}";
		$dumper = new $class();
		return $dumper->getDump($parsed);
	}

	abstract protected function expectedArrayDumpOutput();
	abstract protected function expectedInheritedObjectDumpOutput();
	abstract protected function expectedRecursionObjectDumpOutput();
	abstract protected function expectedSessionDumpOutput();

	final public function getDataArray() {
		$array = array();
		$array["user"] = "user-name";
		$array["network"] = array( "localip" => "1.2.3.4");
		$array[2] = "Zwei";
		$array["language"] = "german";

		return $array;
	}

	final public function &getDataRecursionObject() {
		$object = new \stdClass();
		$object->property = "test";
		$object->float = (float) 1;
		$object->bool = TRUE;
		$object->null = NULL;
		$object->recursion = $object;
		$object->recursionref = &$object;

		return $object;

		return array(&$object);
	}

	final public function getDataInheritedObject() {
		require_once(__DIR__.'/Dumper/testObjects.php');
		return new Dumper\testObjectChild();
	}

	final public function getDataSession() {
		return 'test|i:1;more|a:2:{i:0;i:56;s:3:"key";i:57;}again|i:2;';
	}

	public function provideMultiple() {
		return array(
			array('Array'),
			array('InheritedObject'),
			array('RecursionObject'),
			array('Session', true, 'SessionParser'),
		);
	}

	/**
	 * @dataProvider provideMultiple
	 *
	 * test dumpers agains a specific set of datas defined in this class
	 */
	final public function testDumpOutput($what, $serialized = false, $parser = null) {

		$expectedFunction = sprintf('expected%sDumpOutput', $what);
		$dataFunction = sprintf('getData%s', $what);

		$expected = $this->$expectedFunction();
		$data = &$this->$dataFunction();
		$serialized || $data = serialize($data);
		$actual = $this->getDump($data, $parser);
		$this->assertEquals($expected, $actual);
	}

	/**
     * @expectedException \InvalidArgumentException
     */
	final public function testInvalidArgumentExceptionViaDump() {
		$parsed = array('1');
		$dumper = Dumper::factory($this->dumper);
		$dumper->dump($parsed);
	}

	/**
     * @expectedException \InvalidArgumentException
     */
	final public function testUnkownValueTypeNameExceptionViaDump() {
		$parsed = array('foo', '42');
		$dumper = Dumper::factory($this->dumper);
		$dumper->getDump($parsed);
	}

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
	final public function testDumpParameterException() {
		$dumper = Dumper::factory($this->dumper);
		$dumper->getDump(array(array(), array('illegal option')));
		return;
	}

    /**
     * @expectedException \InvalidArgumentException
     */
	final public function testDumpParameterException2() {
		$dumper = Dumper::factory($this->dumper);
		$dumper->getDump(array());
		return;
	}
}