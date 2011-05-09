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

Namespace Serialized;
Use \InvalidArgumentException;

/**
 * Chars of Value Types
 *
 * Concrete implementation of Value Type Chars (represented as string, more or less defined by PHP itself)
 */
class TypeChars implements ValueTypes {
	private static $typeChars = array(
		self::TYPE_ARRAY => 'a',
		self::TYPE_BOOL => 'b',
		self::TYPE_FLOAT => 'd',
		self::TYPE_INT => 'i',
		self::TYPE_NULL => 'N',
		self::TYPE_OBJECT => 'O',
		self::TYPE_STRING => 's',
		self::TYPE_RECURSION => 'r',
		self::TYPE_RECURSIONREF => 'R',
	);
	/**
	 * get character of type
	 * @param int $type
	 * @throws InvalidArgumentException
	 */
	static public function of($type) {
		if (!isset(self::$typeChars[$type])) {
			throw new InvalidArgumentException(sprintf('Illegal type "%s" - no character for it.', $type));
		}
		return self::$typeChars[$type];
	}
	/**
	 * get type by character
	 * @param string $char
	 * @return int type
	 */
	static public function by($char) {
		$map = array_flip(self::$typeChars);
		if (!isset($map[$char])) {
			throw new InvalidArgumentException(sprintf('Illegal char "%s" - no type for it.', $char));
		}
		return $map[$char];
	}
}