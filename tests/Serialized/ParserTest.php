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
 * @version 0.0.0
 * @package Tests
 */
Namespace Serialized;

require_once(__DIR__ . '/../TestCase.php');

class ParserTest extends TestCase
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

	private function parseArray(array $array){
		$result = array();
		foreach($array as $key => $value) {
			if (!is_scalar($value) && !is_null($value) && !is_array($value) && !is_object($value)) {
				throw new Exception(sprintf('Can handle skalars, NULL, arrays and objects so far, but this is %s.', gettype($value)));
			}
			$keyType = is_int($key) ? 'int' : 'string';
			$keyArray = array($keyType, $key);
			$valueSerialized = serialize($value);
			$valueObject = new Parser($valueSerialized);
			$valueArray = $valueObject->getParsed();
			$result[] = array($keyArray, $valueArray);
		}
		return array('array', $result);
	}

	public function testArray()
	{
		$class = new \StdClass;
		$class->child = new \stdClass;
		$class->child->grandchild = array('a', 'b');
		$class2 = clone $class;
		$class2->child = clone $class2->child;
		$tests = array(
			array(),
			array(1),
			array('a'),
			array(1, 2),
			array('a', 'b'),
			array('a' => '1a', 'b' => '2b'),
			array(1.256E-1),
			array(true),
			array(null),
			array(array()),
			array('a'=>array('aa'=>array('aaa'=>array('aaaa'=>'string value')))),
			array(new \StdClass),
		);
		$i = 0;
		foreach($tests as $test) {
			$value = $test;
			$serialized = serialize($value);
			$expected = $this->parseArray($value);
			$object = new Parser($serialized);
				
			try {
				$result = $object->getParsed();
			} catch (Exception $e) {
				throw $e;
			}
			$this->assertEquals($expected, $result);
		}
	}

	public function testObject()
	{
		# test: object with no members
		
		$object = new \StdClass;
		$expected = array('object', array(
				array('classname', 'stdClass'),
				array('members', array())				
		));
		
		$serialized = serialize($object);
		$parser = new Parser($serialized);
		$actual = $parser->getParsed();

		$this->assertEquals($expected, $actual);
		
		# test: object with member
		
		$object = new \StdClass;
		$object->property='test';
		$expected = array('object', array(
				array('classname', 'stdClass'),
				array('members', array(
					array(
						array('member', 'property'),
						array('string', 'test')
					)
				))
		));

		$serialized = serialize($object);
		$parser = new Parser($serialized);
		$actual = $parser->getParsed();

		$this->assertEquals($expected, $actual);
	}

	public function testRecursion()
	{			
		$o = new \stdClass;
		$o->normal = $o;
		$o->reference = &$o;
		$serialized = serialize($o);
		// O:8:"stdClass":2:{s:6:"normal";r:1;s:9:"reference";R:1;}
		$expected = array('object', array(
						array('classname', 'stdClass'),
						array('members', array(
							array(
								array('member', 'normal'),
								array('recursion', 1)
							),
							array(
								array('member', 'reference'),
								array('recursionref', 1)
							)
						))
		));
		$parser = new Parser($serialized);
		$result = $parser->getParsed();
		$this->assertEquals($expected, $result);
		
		$o = new \stdClass;
		$data = array($o, $o);
		$serialized = serialize($data);
		$expected = array('array', array(
						array(
							array('int', 0),
							array('object', array(
								array('classname', 'stdClass'),
								array('members', array())	
							))
							
						),
						array(
							array('int', 1),
							array('recursion', 2)
						)
						
		));
		$parser->setSerialized($serialized);
		$result = $parser->getParsed();
		$this->assertEquals($expected, $result);
	}
	
	public function testNull()
	{
		$serialized = serialize(null);
		$expectedResult = array('null', null);
		$object = new Parser($serialized);
		$result = $object->getParsed();
		$this->assertEquals($expectedResult, $result);
	}

	public function testValidSkalars()
	{
		$tests = array(
		array(serialize(null), array('null', null)),
		array(serialize(2), array('int', 2)),
		array('i:0002;', array('int', 2)),
		array(serialize(-3), array('int', -3)),
		array(serialize('foobar'), array('string', 'foobar')),
		array('s:0006:"foobar";', array('string', 'foobar')),
		array(serialize(true), array('bool', true)),
		array(serialize(false), array('bool', false)),
		);
		foreach($tests as $test) {
			list($serialized, $expectedResult) = $test;
			$object = new Parser($serialized);
			$result = $object->getParsed();
			$this->assertEquals($expectedResult, $result);
		}
	}

	public function testFloat()
	{
		$tests = array(
		array(serialize(22.4456), 22.4456),
		array(serialize(22.4456E0), 22.4456E0),
		array(serialize(22.4456E-10), 22.4456E-10),
		array(serialize(22.445682828738273287388383827823728378838), 22.445682828738273287388383827823728378838),
		array(serialize((float)1), 1),
		array(serialize(1E+1), 1E+1),
		array(serialize(1E+2), 1E+2),
		array(serialize(1E+10), 1E+10),
		array(serialize(1E+16), 1E+16),
		array(serialize(1E+20), 1E+20),
		array(serialize(1E+30), 1E+30),
		array(serialize(1E-30), 1E-30),
		array(serialize(1E+30000), 1E+30000),
		);
		// works with delta being 0 so far, keeping it as long as this breaks for a good reason.
		$delta = 0;
		foreach($tests as $test) {
			list($serialized, $expectedResult) = $test;
			$object = new Parser($serialized);
			$result = $object->getParsed();
			list($resultType, $resultValue) = $result;
			$this->assertEquals('float', $resultType);
			$this->assertEquals($expectedResult, $resultValue, '', $delta);
		}
	}

	public function testInvalidSerializedStrings()
	{
		$tests = array(
			'N:;',
			'i:2.5;',
			'i:+-25;',
			's:2:"this string tooooo long";',
			's:+2:"this string tooooo long";',
			's:000002:"this string tooooo long";',
			'b:2;',
			'b:-1;',
			'b:;',
			'd:;',
			'd:',
			'd:+INF;',
			'd:+INF',
			'a:1:{d:INF;i:1;}',
			'a:1:{i:1;x:INF;}',
			'a:+1:{i:1;x:INF;}',
			's:0:""',
			's:0:"";overtheend',
			'r:0;',
			'O:8:"stdClass":2:{s:6:"normal";r:a-z;s:9:"reference";R:1;}'
		);
		foreach($tests as $serialized) {
			try {
				$object = new Parser($serialized);
				$this->addToAssertionCount(1);
				$result = $object->getParsed();
			} catch(ParseException $e) {
				continue;
			}
			$this->fail(sprintf('An expected ParseException has not been raised for "%s".', $serialized));
		}
	}

	public function testParse()
	{
		$serialized = 'N;';
		$array = Parser::parse($serialized);
		$expected = array('null', null);
		$this->assertEquals($expected, $array);

		$serialized = ';';
		\PHPUnit_Framework_Error_Warning::$enabled = FALSE;
		$result = @Parser::parse($serialized);
		\PHPUnit_Framework_Error_Warning::$enabled = TRUE;
		$this->assertLastError('Error parsing serialized string: Invalid ("[;]") at offset 0.', 'Parser.php');
		$this->assertSame(false, $result);
		$this->addToAssertionCount(1);
		try {
			$catch = Parser::parse($serialized);
		} catch(\PHPUnit_Framework_Error $e) {
			return;
		}
		$this->fail(sprintf('An expected Exception has not been raised.'));
	}

	public function testGetSerialized()
	{
		$object = new Parser();
		$test = $object->getSerialized();
		$this->assertSame('N;', $test);
	}

	public function testGetParsed()
	{
		$object = new Parser();
		$array = $object->getParsed();
		$expectedArray = array('null', null);
		$this->assertEquals($expectedArray, $array);
	}

	public function testGetType() {
		$test = 'string';
		$expected = 'string';
		$serialized = serialize($test);
		$object = new Parser($serialized);
		$actual = $object->getType();
		$this->assertEquals($expected, $actual);
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

		ob_start();
		$parser->dump();
		$actual = ob_get_clean();

		$this->assertEquals($expected, $actual);
	}

	public function testDumpParameterException() {
		$parser = new Parser();
		try {
			$this->addToAssertionCount(1);
			$parser->dump(null, array('illegal option'));
		} catch(\InvalidArgumentException $e) {
			return;
		}
		$this->fail(sprintf('An expected Exception has not been raised for "%s".', $serialized));
	}

	public function testUnkownValueTypeNameExceptionViaDump() {
		$parsed = array('foo', '42');
		$parser = new Parser();

		ob_start();
		try {
			$parser->dump($parsed);
		} catch(\InvalidArgumentException $e) {
			ob_end_clean();
			$this->addToAssertionCount(1);
			return;
		}
		ob_end_clean();
		$this->fail('An expected exception was not thrown.');
	}
}

