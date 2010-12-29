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
 * @version 0.1.0
 * @package Tests
 */
Namespace Serialized;

require_once(__DIR__.'/TestCase.php');

class DataTest extends TestCase
{
	private function lintDataFile($fileName) {
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

	private function runDataFile($fileName)
	{
		$datas = require $fileName;

		$parser = new \Serialized\Parser();
		printf("%s:\n", basename($fileName));		
		foreach($datas as $index => $serialized) {
			printf(' % 2d ... ', $index + 1);
			$start = microtime(true);
			$parser->setSerialized($serialized);
			$parsed = $parser->getParsed();
			$passed = microtime(true) - $start;
			$nodes = 0;
			$arrays = 0;
			array_walk_recursive($parsed, function($item, $key) use (&$nodes, &$arrays) {
				$nodes++;
				($key==0) && ($item=='array') && $arrays++;
			});
			printf("%f (% 6d bytes / % 5d nodes / % 4d arrays)\n", $passed, strlen($serialized), $nodes, $arrays);
		}
	}

	private function dataTest($data) {
		$fileName = $this->getDataPath().'/'.$data.'.php';
		$this->addToAssertionCount(1);
		list($lint, $lines) = $this->lintDataFile($fileName);
		if ($lint==1) {
			$this->fail(sprintf("Data %s lint failed:%s", $data, implode("\n  - ", $lines)));
			return;
		}
		$this->addToAssertionCount(1);
		$this->runDataFile($fileName);
	}

	private function getDataPath() {
		return __DIR__.'/../data';
	}

	private function getData() {
		$path = $this->getDataPath();
		$mask = '??-*.php';
		return array_map(function($file){return substr(basename($file),0,-4);}, glob($path.'/'.$mask));
	}

	/**
	 * @group data
	 */
	public function testData() {
		$datas = $this->getData();
		foreach($datas as $data) {
			$this->dataTest($data);
		}
	}
}