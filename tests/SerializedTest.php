<?php
/**
 * Serialized - PHP Library for Serialized Data
 *
 * Copyright (C) 2010 Tom Klingenberg, some rights reserved
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
 * @version 0.1.1
 * @package Tests
 */

require_once(__DIR__.'/TestCase.php');

/**
 * Test class for Serialized.
 */
class SerializedTest extends \Serialized\TestCase
{
	/**
	 * @var Serialized
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		\Serialized::unregisterAutoload();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testFileNameOfClassName()
	{
		$tests = array(
			array('Serialized\\ParseException', 'Serialized/ParseException.php'),
		);
		foreach($tests as $test) {
			list($className, $expectedFileName) = $test;
			$expectedFileName = str_replace('/', DIRECTORY_SEPARATOR, $expectedFileName);
			$fileName = \Serialized::fileNameOfClassName($className);
			$this->assertSame($expectedFileName, $fileName);
		}
	}

	public function testFileNameOfClassNameExceptions() {
		try {
			\Serialized::fileNameOfClassName(null);
		} catch(\InvalidArgumentException $e) {
			$this->addToAssertionCount(1);
			try {
				\Serialized::fileNameOfClassName('WhatAFake\\ofMake\\');
			} catch (\InvalidArgumentException $e) {
				$this->addToAssertionCount(1);
				return;
			}
		}
		$this->fail('An expected Exception has not been raised.');
	}

	public function testRegisterAutloloader()
	{
		\Serialized::registerAutoload();
		$result = \Serialized::autoloadRegistered();
		$this->assertSame(TRUE, $result);

		\Serialized::unregisterAutoload();
		$result = \Serialized::autoloadRegistered();
		$this->assertSame(FALSE, $result);
	}

	/**
	 * @todo Implement testLoadClass().
	 */
	public function testLoadClass()
	{
		$className = 'JagTalaSvenskaJaJa';
		$result = \Serialized::loadClass($className);
		$this->assertFalse($result);

		$className = 'Serialized\\Value';
		$result = \Serialized::loadClass($className);
		$this->assertTrue($result, 'Could not load interface- result not true.');

		$className = 'Serialized\\Parser';
		$result = \Serialized::loadClass($className);

		$this->assertTrue($result, 'Could not load class - result not true.');

		$testClass = new $className();

		$this->assertInstanceOf($className, $testClass);
	}

	/**
	 * @todo Implement testLoadLibrary().
	 */
	public function testLoadLibrary()
	{
		\Serialized::loadLibrary();
	}
}
