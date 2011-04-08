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
 * @version 0.1.5
 * @package Tests
 */

Namespace Serialized\Dumper;
Use Serialized\DumperTest;
Use Serialized\Parser;

require_once(__DIR__.'/../DumperTest.php');

class XMLTest extends DumperTest
{
	protected $dumper = 'XML';

	public function expectedArrayDumpOutput() {
		return '<?xml version="1.0"?>
<serialized>
  <array members="4">
    <item name="user" type="string">
      <string len="9" value="user-name"/>
    </item>
    <item name="network" type="string">
      <array members="1">
        <item name="localip" type="string">
          <string len="7" value="1.2.3.4"/>
        </item>
      </array>
    </item>
    <item name="2" type="int">
      <string len="4" value="Zwei"/>
    </item>
    <item name="language" type="string">
      <string len="6" value="german"/>
    </item>
  </array>
</serialized>'."\n";
	}

	public function expectedDumpOutput()
	{
		return '<?xml version="1.0"?>
<serialized>
  <object class="stdClass" members="6">
    <property name="property" access="public">
      <string len="4" value="test"/>
    </property>
    <property name="float" access="public">
      <float value="1"/>
    </property>
    <property name="bool" access="public">
      <bool value="true"/>
    </property>
    <property name="null" access="public">
      <null/>
    </property>
    <property name="recursion" access="public">
      <recursion value="1"/>
    </property>
    <property name="recursionref" access="public">
      <recursionref value="1"/>
    </property>
  </object>
</serialized>'."\n";
	}

	public function expectedObjectDumpOutput() {
		return '<?xml version="1.0"?>
<serialized>
  <object class="Serialized\Dumper\testObjectChild" members="6">
    <property class="Serialized\Dumper\testObjectChild" name="ca" access="private">
      <string len="7" value="private"/>
    </property>
    <property name="cb" access="protected">
      <string len="9" value="protected"/>
    </property>
    <property name="cc" access="public">
      <string len="6" value="public"/>
    </property>
    <property class="Serialized\Dumper\testObjectParent" name="pa" access="private">
      <string len="15" value="private, parent"/>
    </property>
    <property name="pb" access="protected">
      <string len="17" value="protected, parent"/>
    </property>
    <property name="pc" access="public">
      <string len="14" value="public, parent"/>
    </property>
  </object>
</serialized>'."\n";
	}

	public function testOptions() {
		$tags = array('root' => 'dig');

		$dumper = new XML();
		$dumper->setTags($tags);
		$dumper->setNewline('');
		$dumper->setInset('');

		$parsed = array('null', NULL);

		$expected = '<?xml version="1.0"?><dig><null/></dig>';

		ob_start();
		$dumper->dump($parsed);
		$actual = ob_get_clean();

		$this->assertSame($expected, $actual);
	}

	private function assertDTD($xml, $dtd, $message='') {
		$root = 'serialized';
		$version = '1.0';
		$encoding = 'utf-8';
		
		$lDoc = new \DOMDocument($version, $encoding);
		$lDoc->loadXML($xml);
		$lNode = $lDoc->getElementsByTagName($root)->item(0);
		
		$aImp = new \DOMImplementation;
		$aDocType = $aImp->createDocumentType($root, '', $dtd);		
		$aDoc = $aImp->createDocument('', '', $aDocType);
		$aDoc->encoding = $encoding;
		$aNode = $aDoc->importNode($lNode, true);
		$aDoc->appendChild($aNode);
		
		$expected = true;
		$actual = $aDoc->validate();
		$this->assertSame($expected, $actual, $message);
	}

	public function testDTDValidity()
	{
		$name = 'serialized.dtd';
		$path = __DIR__.'/../../../examples/'.$name;
		$data = file_get_contents($path);
		$dtd = 'data://text/plain;base64,'.base64_encode($data);
		
		foreach( array('ArrayDump', 'Dump', 'ObjectDump') as $proc) {
			$func = sprintf('expected%sOutput', $proc);
			$xml = $this->$func();
			$this->assertDTD($xml, $dtd, sprintf('DTD in %s.', $proc));	
		}
	}

}