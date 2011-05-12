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
 * @version 0.2.3
 * @package Examples
 */

Namespace Serialized;
Use Serialized\Dumper\XML as DumperXml;

require_once(__DIR__.'/../../src/Serialized.php');

$string = array(
		'fistArrayItem-index-0' => "TestString",
		'2ndArrayItem-index-1' => "AnotherString",
		'SomeArray' => array( 'Nulltes', 'Erstes')
);

$serialized = serialize($string);
$parser = new Parser($serialized);
$parsed = $parser->getParsed();
// <!ELEMENT arrayitem (string|array|int|bool|null|object)* >
$doctypeInline = '<!DOCTYPE serialized [

	<!ELEMENT serialized (array|object|string|null)* >

	<!ELEMENT array (EMPTY|aitem+)* >
	<!ATTLIST array members NMTOKEN #REQUIRED >

	<!ELEMENT item ANY >
	<!ATTLIST item name NMTOKEN #REQUIRED >

	<!ELEMENT string EMPTY >
	<!ATTLIST string value NMTOKEN #REQUIRED >

	<!ELEMENT null EMPTY >
	<!ATTLIST null value NMTOKEN #REQUIRED >

	<!ELEMENT object (EMPTY|aproperty+) >
	<!ATTLIST object members NMTOKEN #REQUIRED >
	<!ATTLIST object name CDATA #REQUIRED >

	<!ELEMENT property ( string?, array?, null? ) >
	<!ATTLIST property class CDATA >
	<!ATTLIST property access CDATA #REQUIRED >
	<!ATTLIST property name NMTOKEN #REQUIRED >

]>';
$dumper = new DumperXml();

$config = array(
	'doctype' => $doctypeLinked = '<!DOCTYPE serialized SYSTEM "serialized.dtd">',
);

$dumper->dump($parsed, $config);
