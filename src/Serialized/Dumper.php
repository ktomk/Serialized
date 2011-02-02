<?php
/**
 * Serialized - PHP Library for Serialized Data
 * 
 * Copyright (C) 2010 Tom Klingenberg, some rights reserved
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
 * @version 0.1.2
 * @package Serialized
 */

Namespace Serialized;

/**
 * Serialize Dumper
 * 
 * @todo solve typeByName() code duplication (taken from Parser)  
 */
class Dumper implements ValueTypes {
	private $typeNames = array(
		self::TYPE_INVALID => 'invalid',
		self::TYPE_BOOL => 'bool',
		self::TYPE_FLOAT => 'float',
		self::TYPE_INT => 'int',
		self::TYPE_NULL => 'null',
		self::TYPE_RECURSION => 'recursion',
		self::TYPE_RECURSIONREF => 'recursionref',
		self::TYPE_ARRAY => 'array',
		self::TYPE_OBJECT => 'object',
		self::TYPE_STRING => 'string',
		self::TYPE_CLASSNAME => 'classname',
		self::TYPE_MEMBERS => 'members',
		self::TYPE_MEMBER => 'member',
	);
	private function typeByName($name) {
		$map = array_flip($this->typeNames);
		if (!isset($map[$name])) {
			throw new \InvalidArgumentException(sprintf('Unknown name "%s" to identify a vartype.', $name));
		}
		return $map[$name];
	}
	private function dumpStringNice($string) {
		$replace = array(
			array(chr(0), "\n", "\t", "\"",),
			array('\x00', '\n', '\t', '\"',),
		);
		$string = str_replace($replace[0], $replace[1], $string);
		return $string;
	}
	private function dumpValue($type, $value) {
		switch($type) {
			case self::TYPE_ARRAY:
			case self::TYPE_MEMBERS:
				return sprintf('(%d):', count($value));
			case self::TYPE_CLASSNAME:
				return ': '. $value;
			case self::TYPE_MEMBER:
				return ': '. $this->dumpStringNice($value);
			case self::TYPE_STRING:
				return sprintf(': "%s"', $this->dumpStringNice($value));
			case self::TYPE_INT:
			case self::TYPE_FLOAT:
				return ': '.$value;
			case self::TYPE_OBJECT:
				return ':';
			case self::TYPE_NULL:
				return ': NULL';
			case self::TYPE_BOOL:
				return ': ' . ($value ? 'TRUE' : 'FALSE');
			case self::TYPE_RECURSION:
				return ': ' . $value;
			case self::TYPE_RECURSIONREF:
				return ': &' . $value;				
			// @codeCoverageIgnoreStart
			default:
				throw new \InvalidArgumentException(sprintf('Type %s unknonwn.', $type));
		}
	}
	// @codeCoverageIgnoreEnd
	/**
	 * print serialized array notation
	 *  
	 * @param array $parsed serialized array notation data.
	 * @param array $context dumpp context, unused on first call, needed for recursion only.
	 */
	private function dumpImpl(array $parsed, array $context = null) {
		static $level = 0;
		static $printInset = '';
		$printInsetStarts = '| ';
		$printInsetSpace = '    ';
		$printPointStarts = '+`';
		$printPoint = '*--';
		if (0 === $level) {
			if (null !== $context) {
				throw new \InvalidArgumentException('Providing Context is illegal. Use a single argument only.');
			}
			$context = array(1,1); 
		}
		list($index, $count) = $context;
		
		if (($parsedCount = count($parsed)-1) && is_array($parsed[0])) {
			foreach($parsed as $arrayIndex => $element) {
				$this->dumpImpl($element, array($index-($arrayIndex==$parsedCount?0:1), $count));
			}
		} else {
			list($typeName, $value) = $parsed;
			$type = $this->typeByName($typeName);
		
			$printPoint[0] = $printPointStarts[(int)($index===$count)];
			$valueString = $this->dumpValue($type, $value);
			printf("%s%s %s%s\n", $printInset, $printPoint, $typeName, $valueString);
			$isComposite =  self::TYPE_OBJECT===$type || self::TYPE_ARRAY===$type || self::TYPE_MEMBERS===$type; 
			if ($isComposite) {
				$level++;
				$printStack = $printInset;
				$printInset .= $printInsetStarts[(int)($index===$count)].$printInsetSpace;
				$countChildren = count($value);
				$indexChildren = 0;
				foreach($value as $element) {
					$indexChildren++;
					$this->dumpImpl($element, array($indexChildren, $countChildren));
				}
				$printInset = $printStack;
				$level--;
			}
		}	
	}
	/**
	 * print serialized array notation
	 *  
	 * @param array $parsed serialized array notation data.
	 */
	public function dump(array $parsed) {
		$this->dumpImpl($parsed);
	}
}