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

Namespace Serialized\Dumper;
Use Serialized\Dumper;
Use Serialized\DumperTest;
Use Serialized\Parser;

require_once(__DIR__.'/../DumperTest.php');

class XMLTest extends DumperTest
{
	protected $dumper = 'XML';

	public function testOptions() {
		$dumper = new XML();
		$config = array(
			'newline' => '',
			'indent' => '',
			'tags' => array(
				'root' => 'dig',
			),
		);

		$parsed = array('null', NULL);

		$expected = '<?xml version="1.0" encoding="us-ascii"?><dig><null/></dig>';

		ob_start();
		$dumper->dump($parsed, $config);
		$actual = ob_get_clean();

		$this->assertSame($expected, $actual);
	}

	public function testConfig() {
		$config = array(
			'declaration' => '<?xml version="1.0" encoding="UTF-8" ?>',
			'newline' => "\n",
			'indent' => "\t",
			'tags' => array(
				'root' => 'dig',
			),
		);

		$parsed = array('null', NULL);
		$expected = '<?xml version="1.0" encoding="UTF-8" ?>
<dig>
	<null/>
</dig>
';

		$dumper = new XML();
		ob_start();
		$dumper->dump($parsed, $config);
		$actual = ob_get_clean();

		$this->assertSame($expected, $actual);
	}

	private function callEncoding($string) {
		static $selfObject;
		empty($selfObject) &&
			$selfObject = Dumper::factory('xml')
			;
		$object = new \ReflectionObject($selfObject);
		$method = $object->getMethod('xmlAttributeEncode');
		$method->setAccessible(true);

		$args = array($string);
		return $method->invokeArgs($selfObject, $args);
	}

	public function testAttributeEncoding() {
		$data = array(
			"" => "",
			"\0" => "&#x0;",
			"\t" => "&#x9;",
			'"' => "&quot;",
			'&' => "&amp;",
			'<' => "&lt;",
			'>' => "&gt;",
			"1 & 2 are < 3 and > 0 \n" => "1 &amp; 2 are &lt; 3 and &gt; 0 &#xA;",
			"Strings are expressed \"within double quotes\" in many computer languages." => "Strings are expressed &quot;within double quotes&quot; in many computer languages.",
		);
		foreach($data as $string => $expected) {
			$actual = $this->callEncoding($string);
			$this->assertSame($expected, $actual);
		}
	}

	private function assertDTD($xml, $dtd, $message='')
	{
		$importDoc = new \DOMDocument();
		$importDoc->loadXML($xml);

		$rootNode = $importDoc->documentElement;
		$rootName = $rootNode->tagName;
		$version = $importDoc->xmlVersion;
		$encoding = $importDoc->encoding;

		$assertImplementation = new \DOMImplementation;
		$assertDocType = $assertImplementation->createDocumentType($rootName, '', $dtd);
		$assertDoc = $assertImplementation->createDocument('', '', $assertDocType);
		$assertDoc->xmlVersion = $version;
		$assertDoc->encoding = $encoding;
		$importNode = $assertDoc->importNode($rootNode, true);
		$assertDoc->appendChild($importNode);

		$expected = true;
		$actual = $assertDoc->validate();
		$this->assertSame($expected, $actual, $message);
	}

	public function testDTDValidity()
	{
		$name = 'serialized.dtd';
		$path = __DIR__.'/../../../examples/'.$name;
		$data = file_get_contents($path);
		$dtd = 'data://text/plain;base64,'.base64_encode($data);

		foreach( array('1001', '1002', '1003', 'session-01') as $proc) {
			$xml = $this->getExpected($proc);
			$this->assertDTD($xml, $dtd, sprintf('DTD in %s.', $proc));
		}
	}

}