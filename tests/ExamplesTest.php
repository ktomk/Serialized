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

	private function lintExampleFile($fileName) {
		$return = 0;
		$lintOutput = array();
		$exitStatus = 0;
		exec("php -l " . escapeshellarg($fileName), $lintOutput, $return);
		if ($return != 0) {
			$exitStatus = 1;
			array_splice($lintOutput, -2);
		}
		return array($exitStatus, $lintOutput);
	}

	private function runExampleFile($fileName)
	{
		ob_start();
		require $fileName;
		ob_end_clean();
	}

	private function exampleTest($example) {
		$fileName = $this->getExamplesPath().'/'.$example.'.php';
		$this->addToAssertionCount(1);
		list($lint, $lines) = $this->lintExampleFile($fileName);
		if ($lint==1) {
			$this->fail(sprintf("Example %s lint failed:%s", $example, implode("\n  - ", $lines)));
			return;
		}
		$this->addToAssertionCount(1);
		$this->runExampleFile($fileName);
	}

	private function getExamplesPath() {
		return __DIR__.'/../examples';
	}

	private function getExamples() {
		$path = $this->getExamplesPath();
		$mask = '??-*.php';
		return array_map(function($file){return substr(basename($file),0,-4);}, glob($path.'/'.$mask));
	}

	public function testExamples() {
		$examples = $this->getExamples();
		foreach($examples as $example) {
			$this->exampleTest($example);
		}
	}
}