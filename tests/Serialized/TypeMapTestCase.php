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
Use \InvalidArgumentException;

require_once(__DIR__.'/../TestCase.php');

/**
 * Test class for TypeMap.
 * Generated by PHPUnit on 2011-05-08 at 19:21:29.
 */
abstract class TypeMapTestCase extends TestCase
{
	protected $testClass;
	protected $testType;
	protected $testValue;
	protected $testNonExistantType;
	protected $testNonExistantValue;
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
	protected function mapTestHas($class, $type, $nonExistantType = null) {
		$class = __NAMESPACE__.'\\'.$class;
		# test existing
		$expected = true;
		$actual = $class::has($type);
		$this->assertSame($expected, $actual);

		# test non-existing
		$expected = false;
		$actual = $class::has($nonExistantType);
		$this->assertSame($expected, $actual);
	}
	protected function mapTestOf($class, $type, $expected, $nonExistantType = null) {
		$class = __NAMESPACE__.'\\'.$class;
		# test existing
		$actual = $class::of($type);
		$this->assertSame($expected, $actual);

		# test non-existing (provoke exception)
		$class::of($nonExistantType);
	}
	protected function mapTestBy($class, $value, $expected, $nonExistantValue = null) {
		$class = __NAMESPACE__.'\\'.$class;
		# test existing
		$actual = $class::by($value);
		$this->assertSame($expected, $actual);

		# test non-existing (provoke exception)
		$class::by($nonExistantValue);
	}
	public function testHas() {
		$class = $this->testClass;
		$type = $this->testType;
		$nonExistantType = $this->testNonExistantType;
		$this->mapTestHas($class, $type, $nonExistantType);
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOf() {
		$class = $this->testClass;
		$type = $this->testType;
		$expected = $this->testValue;
		$this->mapTestOf($class, $type, $expected);
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBy() {
		$class = $this->testClass;
		$value = $this->testValue;
		$expected = $this->testType;
		$this->mapTestBy($class, $value, $expected);
	}
}