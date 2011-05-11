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

require_once(__DIR__.'/TestCase.php');

/**
 * Test Examples Class
 *
 * The exmaples test iterates over all
 * examples that are shipping with the library,
 * asserting lints and execution.
 */
class ExamplesTest extends TestCase
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

	private function runExampleFile($fileName)
	{
		ob_start();
		require $fileName;
		ob_end_clean();
	}

	private function exampleTest($example) {
		$fileName = $this->getExamplesPath().'/'.$example.'.php';
		$this->assertLint($fileName);
		$this->runExampleFile($fileName);
		return true;
	}

	private function getExamplesPath() {
		return __DIR__.'/../examples';
	}

	private function getExamples() {
		$path = $this->getExamplesPath();
		$examples = glob("{$path}/[0-9][0-9]-?*/example-?*.php", GLOB_NOSORT);
		return array_map(function($file){return basename(dirname($file)) . '/' . substr(basename($file),0,-4);},$examples);
	}

	public function examplesProvider() {
		return array_map(function($entry){return array($entry);}, $this->getExamples());
	}

	/**
	 * @dataProvider examplesProvider
	 */
	public function testExample($example) {
		$expected = true;
		$actual = $this->exampleTest($example);
		$this->assertSame($expected, $actual);
	}

	public function testExamples() {
		$examples = $this->getExamples();
		$this->assertInternalType('array', $examples, 'Expected examples to test.');
		$this->assertGreaterThan(0, count($examples), 'Expected at least one example to test.');
	}
}