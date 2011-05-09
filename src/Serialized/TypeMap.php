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
abstract class TypeMap implements ValueTypes {
	protected static $valType = '[value type of map]';
	protected static $map = array();
	/**
	 * get [value] of type
	 * @param int $type
	 * @return string [value]
	 * @throws InvalidArgumentException
	 */
	static public function of($type) {
		if (!isset(static::$map[$type])) {
			throw new InvalidArgumentException(sprintf('Illegal type "%s" - no %s for it.', $type, static::$valType));
		}
		return static::$map[$type];
	}
	/**
	 * get type by value
	 * @param string $value
	 * @return int type
	 */
	static public function by($value) {
		$map = array_flip(static::$map);
		if (!isset($map[$value])) {
			throw new InvalidArgumentException(sprintf('Illegal value "%s" - not a %s.', $value, static::$valType));
		}
		return $map[$value];
	}
}