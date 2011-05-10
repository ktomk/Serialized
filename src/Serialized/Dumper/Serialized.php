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
 * @package Serialized
 */

Namespace Serialized\Dumper;
Use Serialized\Dumper;
Use Serialized\TypeNames;
Use Serialized\TypeChars;

/**
 * Serialize Dumper
 *
 * Default dumper, works in the shell.
 */
class Serialized extends Dumper implements Concrete {
	private function dumpArray(array $items) {
		$char = TypeChars::of(self::TYPE_ARRAY);
		printf('%s:%d:{', $char, count($items));
		foreach($items as $item) {
			$this->dumpAny($item[0]);
			$this->dumpAny($item[1]);
		}
		print('}');
	}
	private function dumpBool($bool) {
		print serialize($bool);
	}
	private function dumpInt($int) {
		$char = TypeChars::of(self::TYPE_INT);
		printf('%s:%d;', $char, $int);
	}
	private function dumpFloat($float) {
		print serialize($float);
	}
	private function dumpMembers($members) {
		$count = count($members);
		printf('%d:{', $count);
		foreach($members as $member) {
			list(list($property, $name), $value) = $member;
			$this->dumpString($name);
			$this->dumpAny($value);
		}
		print('}');
	}
	private function dumpNull($null) {
		print('N;');
	}
	private function dumpObject(array $object) {
		$char = TypeChars::of(self::TYPE_OBJECT);
		$classname = $object[0][1];
		printf('%s:%d:"%s":', $char, strlen($classname), $classname);
		$this->dumpAny($object[1]);
		// "O:8:\"stdClass\":2:{s:6:\"normal\";r:1;s:9:\"reference\";R:1;}"
	}
	private function dumpRecursion($recursion) {
		printf('r:%d;', $recursion);
	}
	private function dumpRecursionref($recursionref) {
		printf('R:%d;', $recursionref);
	}
	private function dumpString($string) {
		$char = TypeChars::of(self::TYPE_STRING);
		printf('%s:%d:"', $char, strlen($string));
		print($string);
		print('";');
	}
	private function dumpAny(array $parsed) {
		list($typeName, $value) = $parsed;
		$type = TypeNames::by($typeName);
		$function = sprintf('dump%s', ucfirst($typeName));
		$this->$function($value);
	}
	/**
	 * print serialized array notation as serialized data (PHP)
	 *
	 * @param array $parsed
	 */
	protected function dumpConcrete(array $parsed) {
		$this->dumpAny($parsed);
	}
}