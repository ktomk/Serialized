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

class SessionParserTest extends TestCase
{
	/**
	 * @var SessionParser
	 */
	private $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new SessionParser();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testGetParsed() {

		$parser = $this->object;

		// empty session
		$expected = array();
		$parser->setSession('');
		$actual = $parser->getParsed();
		$this->assertSame($expected, $actual);

		// session with some data
		$session = 'test|i:1;'; // more|a:2:{i:0;i:56;s:3:"key";i:57;}again|i:2;';
		$expected = array();
		$parser->setSession($session);
		$actual = $parser->getParsed();

	}

	public function testInvalidSessionStrings() {
		$tests = array(
			'sks|;',
			'0sks|;',
			'-sks|N;',
			'sk-s|i:1;',
		);
		foreach($tests as $session) {
			try {
				$object = new SessionParser($session);
				$this->addToAssertionCount(1);
				$object->getParsed();
			} catch(ParseException $e) {
				continue;
			}
			$this->fail(sprintf('An expected ParseException has not been raised for "%s".', $session));
		}
	}

	public function testParseVariables() {
		$parser = $this->object;
		$parser->setSession('..test|i:1;');
		$this->setExpectedException('Serialized\ParseException');
		$parser->parseVariables(1024); // offset is out of bounds
	}
}