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
 * @version 0.1.6
 * @package Serialized
 */

Namespace Serialized\Dumper;
Use Serialized\Dumper;
Use \Exception;

/**
 * Text Dumper
 *
 * Default dumper, works in the shell.
 */
class Text extends Dumper implements Concrete {
	/**
	 * master be be used as datasource (two bytes)
	 * @var string
	 */
	private $printInsetStarts = '| ';
	/**
	 * inset configuration
	 * @var string
	 */
	private $printInsetSpace = '    ';
	/**
	 * master to be used as datasource (two bytes)
	 * @var string
	 */
	private $printPointStarts = '+`';
	/**
	 * pattern to be modified
	 * @var string
	 */
	private $printPoint = '*--';
	private function printInset($isLast) {
		$printPoint = $this->printPoint;
		$printPoint[0] = $this->printPointStarts[(int) $isLast];
		printf("%s%s", $this->state->inset, $printPoint);
	}
	private function dumpArrayElement(array $element) {
		list(list($keyType, $keyValue)) = $element;
		if($keyType === 'int') {
			$keyValue = (int) $keyValue;
		} elseif ($keyType === 'string') {
			;
		} else {
			// @codeCoverageIgnoreStart
			throw new \InvalidArgumentException(sprintf('Invalid type for array key #%d: "%s".', $index, $keyType));
		}	// @codeCoverageIgnoreEnd
		$keyValue = sprintf('[%s]', $keyValue);
		return $keyValue;
	}
	private function dumpArray($value) {
		$count = count($value);
		if (!$count--)
			return;

		foreach($value as $index => $element) {
			$isLast = $index === $count;
			$keyValue = $this->dumpArrayElement($element);

			list($typeName, $valueValue) = $element[1];
			$type = $this->typeByName($typeName);
			$valueString = $this->dumpValue($type, $valueValue);

			$this->printInset($isLast);
			printf(" %s => %s%s\n", $keyValue, $typeName, $valueString);
			$this->dumpSubValue($type, $valueValue, $isLast);
		}
	}
	private function dumpObjectMember(array $member) {
			list(list(, $memberName)) = $member;
			$memberAccess = '';
			if("\x00"==$memberName[0]) {
				if ("\x00*\x00"==substr($memberName, 0, 3)) {
					$memberName = substr($memberName, 3);
					$memberAccess = ' (protected)';
				} elseif (false !== $pos = strpos($memberName, "\x00", 1)) {
					$memberAccess = ' ('.substr($memberName,1, $pos-1).':private)';
					$memberName = substr($memberName, $pos+1);
				} else {
					// @codeCoverageIgnoreStart
					throw new \InvalidArgumentException(sprintf('Invalid member-name: "%s".', $memberName));
				}	// @codeCoverageIgnoreEnd
			}
			$memberName = sprintf('[%s]%s', $memberName, $memberAccess);
			return $memberName;
	}
	private function dumpObject($value) {
		$classname = $value[0][1];
		$members = $value[1][1];
		$count = count($members);
		if (!$count--)
			return;

		foreach($members as $index => $element) {
			$isLast = $index === $count;
			$memberName = $this->dumpObjectMember($element);
			list(, $valueArray) = $element;

			list($typeName, $valueValue) = $valueArray;
			$type = $this->typeByName($typeName);
			$valueString = $this->dumpValue($type, $valueValue);

			$this->printInset($isLast);
			printf(" %s -> %s%s\n", $memberName, $typeName, $valueString);
			$this->dumpSubValue($type, $valueValue, $isLast);
		}
	}
	private function dumpSubValue($type, $value, $isLast) {
		$subDumpMap = array(
			self::TYPE_ARRAY => 'dumpArray',
			self::TYPE_OBJECT => 'dumpObject',
		);
		if (false === array_key_exists($type, $subDumpMap))
			return;

		$this->statePush();
		$this->state->inset .= $this->printInsetStarts[(int) $isLast] . $this->printInsetSpace;

		$method = $subDumpMap[$type];
		$this->$method($value);
		$this->statePop();
	}
	private function dumpValue($type, $value) {
		switch($type) {
			case self::TYPE_ARRAY:
			case self::TYPE_MEMBERS:
				return sprintf('(%d)%s', count($value), count($value)?':':'.');
			case self::TYPE_STRING:
				return sprintf('(%d): "%s"', strlen($value), $this->dumpStringNice($value));
			case self::TYPE_INT:
			case self::TYPE_FLOAT:
				return ': '.$value;
			case self::TYPE_OBJECT:
				$count = count($value[1][1]);
				return sprintf('(%s) (%d)%s', $value[0][1], $count, $count?':':'.');
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
	private function dumpAny(array $parsed) {
		if (count($parsed) != 2) {
			throw new \InvalidArgumentException('Parameter is expected to be an array of two values.');
		}
		list($type, $typeName, $valueValue) = $this->typeExport($parsed);
		$valueString = $this->dumpValue($type, $valueValue);
		$this->printInset(1);
		printf(" %s%s\n", $typeName, $valueString);
		$this->dumpSubValue($type, $valueValue, true);
	}
	/**
	 * print serialized array notation
	 *
	 * @param array $parsed serialized array notation data.
	 */
	public function dump(array $parsed) {
		$this->dumpAny($parsed);
	}
}
