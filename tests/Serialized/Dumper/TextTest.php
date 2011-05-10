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

class TextTest extends DumperTest
{
	protected $dumper = 'Text';

	protected function expectedArrayDumpOutput() {
		return '`-- array(4):
     +-- [user] => string(9): "user-name"
     +-- [network] => array(1):
     |    `-- [localip] => string(7): "1.2.3.4"
     +-- [2] => string(4): "Zwei"
     `-- [language] => string(6): "german"'."\n";
	}

	protected function expectedRecursionObjectDumpOutput() {
		return '`-- object(stdClass) (6):
     +-- [property] -> string(4): "test"
     +-- [float] -> float: 1
     +-- [bool] -> bool: TRUE
     +-- [null] -> null: NULL
     +-- [recursion] -> recursion: 1
     `-- [recursionref] -> recursionref: &1'."\n";
	}

	protected function expectedInheritedObjectDumpOutput() {
		return '`-- object(Serialized\Dumper\testObjectChild) (7):
     +-- [ca] (Serialized\Dumper\testObjectChild:private) -> string(7): "private"
     +-- [cb] (protected) -> string(9): "protected"
     +-- [cc] -> string(6): "public"
     +-- [pa] (Serialized\Dumper\testObjectParent:private) -> string(15): "private, parent"
     +-- [pb] (protected) -> string(17): "protected, parent"
     +-- [pc] -> string(14): "public, parent"
     `-- [Éncödïng] (Serialized\Dumper\testÉncödïng:private) -> bool: TRUE'."\n";
	}

	protected function expectedSessionDumpOutput() {
		return '`-- variables (3):
     +-- $test = int: 1
     +-- $more = array(2):
     |    +-- [0] => int: 56
     |    `-- [key] => int: 57
     `-- $again = int: 2'."\n";
	}
}