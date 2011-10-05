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

require_once(__DIR__.'/TestCase.php');

/**
 * Test multiple data
 */
class DataTest extends TestCase
{
	private function runDataFile($fileName)
	{
		$datas = require $fileName;
		if ($datas === 1)
			$datas = array();

		$parser = new Parser();
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

	/**
	 * helper function for @dataProviders
	 *
	 * @param string $dir basename of (user) data directory
	 * @param string $mask glob
	 */
	private function providerFiles($dir, $mask)
	{
		$path = realpath(__DIR__ . '/../data/'.$dir);
		$files = glob($path.'/'.$mask);
		return array_map(function($file){return array($file);}, $files);
	}

	public function providerDataFiles() {
		return $this->providerFiles('serialize', '??-*.php');
	}

	/**
	 * @group data
	 * @dataProvider providerDataFiles
	 */
	public function testData($file) {
		$this->assertLint($file);
		$this->runDataFile($file);
	}

	public function providerSessionFiles() {
		return $this->providerFiles('session', '??-sess*');
	}

	/**
	 * @group data
	 * @dataProvider providerSessionFiles
	 */
	public function testSession($file) {
		$session = file_get_contents($file);
		$parser = new SessionParser($session);
		try {
			$parser->getParsed();
			$dump = $parser->getDump();
		} catch(ParseException $e) {
			$offset = $e->getCode();
			echo "\n", $e->getMessage(), "\n";
			if (null !== $offset) {
				// @see Serialized\Parser::extract() (private)
				$delta = 12;
				$before = $offset - max(0, $offset-$delta);
				$after = min(strlen($session), $offset+1+$delta) - $offset+1;
				echo ($offset-$before > 0) ? '...' : ''
					, substr($session, $offset-$before, $before)
					, '[', $session[$offset], ']'
					, substr($session, $offset+1, $after)
					, ($offset+1+$after < strlen($offset)) ? '...' : ''
					, "\n";
			}
			throw $e;
		}
	}
}