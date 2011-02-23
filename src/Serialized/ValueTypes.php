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
 * @version 0.1.3
 * @package Serialized
 */

Namespace Serialized;

interface ValueTypes {
	const TYPE_INVALID = 1;
	const TYPE_NULL = 11;	
	const TYPE_BOOL = 21;
	const TYPE_INT = 22;
	const TYPE_FLOAT = 23;
	const TYPE_STRING = 24;
	const TYPE_RECURSION = 31;
	const TYPE_RECURSIONREF = 32;
	const TYPE_ARRAY = 41;
	const TYPE_OBJECT = 42;
	const TYPE_CLASSNAME = 101;
	const TYPE_MEMBERS = 102;
	const TYPE_MEMBER = 103;
}