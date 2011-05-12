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

class ParserDataTest extends TestCase
{
	private function getExpected($name) {
		$filename = sprintf('%s/parser/%s-expected', $this->providerGetDataPath(), $name);
		return file_exists($filename) ? file_get_contents($filename) : '';
	}

	private function setLast($name, $actual) {
		$filename = sprintf('%s/parser/%s-last', $this->providerGetDataPath(), $name);
		return file_put_contents($filename, $actual);
	}

	private function output(array $parsed) {
		$parsed = print_r($parsed, true);
		$parsed = explode("\n", $parsed);
		$parsed = array_map('rtrim', $parsed);
		return implode("\n", $parsed);
	}

	/**
	 * @dataProvider providerTestDataSerialize
	 */
	public function testData($name, $testdata) {
		if (is_array($testdata)) {
			$parser = new SessionParser($testdata[0]);
		} else {
			$parser = new Parser($testdata);
		}
		$parsed = $parser->getParsed();
		$expected = $this->getExpected($name);
		$actual = $this->output($parsed);
		$this->setLast($name, $actual);
		$this->assertEquals($expected, $actual);
	}
}