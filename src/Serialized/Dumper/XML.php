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
 * @version 0.2.0
 * @package Serialized
 */

Namespace Serialized\Dumper;
Use Serialized\Dumper;
Use Serialized\TypeNames;
Use \Exception;

/**
 * XML Dumper
 *
 * XML Markup of serialized data.
 */
class XML extends Dumper implements Concrete {
	/**
	 * configuration local store
	 * @var array
	 */
	protected $config = array(
		'declaration' => '<?xml version="1.0" encoding="us-ascii"?>',
		'doctype' => '',
		'newline' => "\n",
		'indent' => '  ',
		'tags' => array(
			'root' => 'serialized',
			'array'  => 'array',
			'array/item'  => 'item',
			'object' => 'object',
			'object/property' => 'property',
			'variables' => 'variables',
			'variables/variable' => 'var'
		),
	);
	/**
	 * push the current state onto the stack
	 */
	protected function statePush() {
		parent::statePush();
		$this->state->inset .= $this->config('indent');
	}
	private function xmlAttributeEncode($string) {
		static $seq = array(0x22 => 'quot', 0x26 => 'amp', 0x3c => 'lt', 0x3e => 'gt');
		for(
			$r = '',
			$l = strlen($string),
			$i = 0
			;
			$i < $l
			;
			$c = $string[$i++],
			$o = ord($c),
			$h = dechex($o),
			($f = (0x22 === $o || 0x26 === $o || 0x3c === $o || 0x3e  === $o)) && $c = '&'.$seq[$o].';',
			$r.= ($f || (0x1F < $o && $o < 0x7F)) ? $c : '&#x'.strtoupper(dechex($o)).';'
		);
		return $r;
	}
	private function dumpArrayElement(array $element) {
		list(list($keyType, $keyValue)) = $element;
		if($keyType === 'int') {
			$keyValue = (string) (int) $keyValue;
		} elseif ($keyType === 'string') {
			;
		} else {
			// @codeCoverageIgnoreStart
			throw new \InvalidArgumentException(sprintf('Invalid type for array key #%d: "%s".', $index, $keyType));
		} 	// @codeCoverageIgnoreEnd
		$keyValue = sprintf(' name="%s" type="%s"', $this->xmlAttributeEncode($keyValue), $keyType);
		return $keyValue;
	}
	private function dumpVariable(array $variable) {
		list(list(, $varName)) = $variable;
		return sprintf(' name="%s"', $this->xmlAttributeEncode($varName));
	}
	private function dumpChildren(array $children, $xmlElement, $callbackElement) {
		$count = count($children);
		if (!$count--)
			return;

		$inset = $this->state->inset;
		$NL = $this->config('newline');

		foreach($children as $index => $child) {
			$keyValue = $this->$callbackElement($child);
			echo $inset, '<', $xmlElement, $keyValue, '>', $NL;
			$this->dumpNode($child[1]);
			echo $inset, '</', $xmlElement, '>', $NL;
		}
	}
	private function dumpArray(array $value) {
		$xmlElement = $this->config['tags']['array/item'];
		$callbackElement = 'dumpArrayElement';
		$this->dumpChildren($value, $xmlElement, $callbackElement);
	}
	private function dumpVariables(array $variables) {
		$xmlElement = $this->config['tags']['variables/variable'];
		$callbackElement = 'dumpVariable';
		$this->dumpChildren($variables, $xmlElement, $callbackElement);
	}
	private function dumpObjectMember(array $member) {
			list(list(, $memberName)) = $member;
			list($name, $class, $access) = $this->parseMemberName($memberName);
			$memberAccess = '';
			switch($access) {
				case 1: $memberAccess = 'protected'; break;
				case 2: $memberAccess = 'private';
			}
			$class &&
				$class = sprintf(' class="%s"', $this->xmlAttributeEncode($class))
				;
			$memberAccess &&
				$memberAccess = sprintf(' access="%s"', $memberAccess)
				;
			return sprintf('%s name="%s"%s', $class, $this->xmlAttributeEncode($name), $memberAccess);
	}
	private function dumpObject($value) {
		$members = $value[1][1];
		$xmlElement = $this->config['tags']['object/property'];
		$callbackElement = 'dumpObjectMember';
		$this->dumpChildren($members, $xmlElement, $callbackElement);
		return;
	}
	private function dumpSubValue($type, $value) {
		$subDumpMap = array(
			self::TYPE_ARRAY => 'dumpArray',
			self::TYPE_CUSTOM => 'dumpCustom',
			self::TYPE_OBJECT => 'dumpObject',
			self::TYPE_VARIABLES => 'dumpVariables',
		);

		if (false === array_key_exists($type, $subDumpMap))
			return;

		$this->statePush();
		$this->$subDumpMap[$type]($value);
		$this->statePop();
	}
	private function dumpCustom(array $custom) {
		$this->dumpNode($custom[1]);
	}
	private function dumpValue($type, $value) {
		switch($type) {
			case self::TYPE_ARRAY:
			case self::TYPE_MEMBERS:
				return sprintf(' members="%s"', count($value));
			case self::TYPE_VARIABLES:
				return sprintf(' count="%s"', count($value));
			case self::TYPE_STRING:
			case self::TYPE_CUSTOMDATA:
				return sprintf(' len="%d" value="%s"', strlen($value), $this->xmlAttributeEncode($value));
			case self::TYPE_INT:
			case self::TYPE_FLOAT:
				return sprintf(' value="%s"', $value);
			case self::TYPE_OBJECT:
				$count = count($value[1][1]);
				return sprintf(' class="%s" members="%d"', $value[0][1], $count);
			case self::TYPE_CUSTOM:
				return sprintf(' class="%s"', $value[0][1]);
			case self::TYPE_NULL:
				return '';
			case self::TYPE_BOOL:
				return sprintf(' value="%s"', ($value ? 'true' : 'false'));
			case self::TYPE_RECURSION:
				return sprintf(' value="%s"', $value);
			case self::TYPE_RECURSIONREF:
				return sprintf(' value="%s"', $value);
			// @codeCoverageIgnoreStart
			default:
				throw new \InvalidArgumentException(sprintf('Type #%s (%s) unhandeled.', $type, TypeNames::of($type)));
		}
	} // @codeCoverageIgnoreEnd
	private function hasInnerElements($type) {
		switch($type) {
			case self::TYPE_ARRAY:
			case self::TYPE_CUSTOM:
			case self::TYPE_OBJECT:
			case self::TYPE_VARIABLES:
				return true;
			// @codeCoverageIgnoreStart
			case self::TYPE_MEMBERS: // pre-caution: should not happen, but if don't return false for those.
				throw new \InvalidArgumentException('TYPE_MEMBERS not wanted for the moment');
			// @codeCoverageIgnoreEnd
			default:
				return false;
		}
	}
	private function dumpNode(array $parsed) {
		list($typeName, $valueValue) = $parsed;
		$type = TypeNames::by($typeName);

		$valueString = $this->dumpValue($type, $valueValue);
		$xmlElement = $typeName;

		$this->statePush();

		if ($this->hasInnerElements($type)) {
			printf('%s<%s%s>%s', $this->state->inset, $xmlElement, $valueString, $this->config('newline'));
			$this->dumpSubValue($type, $valueValue);
			printf("%s</%s>%s", $this->state->inset, $xmlElement, $this->config('newline'));
		} else {
			echo $this->state->inset, '<', $xmlElement, $valueString, '/>', $this->config('newline');
		}

		$this->statePop();
	}
	private function config($name, $addnewline = false) {
		$NL = $addnewline ? $this->config['newline'] : '';
		return empty($this->config[$name]) ? '' : $this->config[$name].$NL;
	}
	private function dumpAny(array $parsed) {
		$xmlRoot = $this->config['tags']['root'];

		$doctype = null;

		echo $this->config('declaration', true);
		echo $this->config('doctype', true);

		echo '<', $xmlRoot, '>', $this->config('newline');

		$this->dumpNode($parsed);

		echo '</', $xmlRoot, '>', $this->config('newline');
	}
	/**
	 * print serialized array notation as XML
	 *
	 * @param array $parsed serialized array notation data.
	 */
	public function dumpConcrete(array $parsed) {
		$this->dumpAny($parsed);
	}
}