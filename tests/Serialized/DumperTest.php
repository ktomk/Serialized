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

	/**
	 * NOTE: this function is re-used by the XMLDumper as well,
	 *       therefore it's protected.
	 * @param
	 */
	protected function getExpected($name) {
		$filename = sprintf('%s/dumper/%s/%s-expected', $this->providerGetDataPath(), $this->dumper, $name);
		return file_exists($filename) ? file_get_contents($filename) : '';
	}

	private function setLast($name, $actual) {
		$filename = sprintf('%s/dumper/%s/%s-last', $this->providerGetDataPath(), $this->dumper, $name);
		return file_put_contents($filename, $actual);
	}

	/**
	 * NOTE: If the Parser Data based Tests fail, this
	 *       test relies on them.
	 *
	 * @dataProvider providerTestDataSerialize
	 */
	public function testData($name, $testdata) {
			if (is_array($testdata)) {
			$parser = new SessionParser($testdata[0]);
		} else {
			$parser = new Parser($testdata);
		}
		$this->setLast($name, 'if you can read this, dumping the test data failed.');
		$actual = $parser->getDump($this->dumper);
		$expected = $this->getExpected($name);
		$this->setLast($name, $actual);
		$this->assertEquals($expected, $actual);
	}
}