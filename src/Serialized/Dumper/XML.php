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
		'declaration' => '<?xml version="1.0"?>',
		'doctype' => '',
		'newline' => "\n",
		'indent' => '  ',
		'tags' => array(
			'root' => 'serialized',
			'array'  => 'array',
			'array/item'  => 'item',
			'object' => 'object',
			'object/property' => 'property',
		),
	);
	/**
	 * push the current state onto the stack
	 */
	protected function statePush() {
		parent::statePush();
		$this->state->inset .= $this->config('indent');
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
		} 	// @codeCoverageIgnoreEnd
		$keyValue = sprintf(' name="%s" type="%s"', htmlspecialchars($keyValue), $keyType);
		return $keyValue;
	}
	private function dumpArray($value) {
		$count = count($value);
		if (!$count--)
			return;
		$xmlElement = $this->config['tags']['array/item'];
		$inset = $this->state->inset;
		$NL = $this->config('newline');

		foreach($value as $index => $element) {
			$keyValue = $this->dumpArrayElement($element);
			echo $inset, '<', $xmlElement, $keyValue, '>', $NL;
			$this->dumpNode($element[1]);
			echo $inset, '</', $xmlElement, '>', $NL;
		}
	}
	private function dumpObjectMember(array $member) {
			list(list(, $memberName)) = $member;
			$memberAccess = 'public';
			$memberClass = '';
			if("\x00"==$memberName[0]) {
				if ("\x00*\x00"==substr($memberName, 0, 3)) {
					$memberName = substr($memberName, 3);
					$memberAccess = 'protected';
				} elseif (false !== $pos = strpos($memberName, "\x00", 1)) {
					$memberAccess = 'private';
					$memberClass = sprintf(' class="%s"', substr($memberName,1, $pos-1));
					$memberName = substr($memberName, $pos+1);
				} else {
					// @codeCoverageIgnoreStart
					throw new \InvalidArgumentException(sprintf('Invalid member-name: "%s".', $memberName));
				} 	// @codeCoverageIgnoreEnd
			}
			return sprintf('%s name="%s" access="%s"', $memberClass, $memberName, $memberAccess);
	}
	private function dumpObject($value) {
		$classname = $value[0][1];
		$members = $value[1][1];
		$count = count($members);
		if (!$count--)
			return;

		$inset = $this->state->inset;
		$NL = $this->config('newline');
		$xmlElement = $this->config['tags']['object/property'];

		foreach($members as $index => $element) {
			$xmlAttributes = $this->dumpObjectMember($element);

			echo $inset, '<', $xmlElement, $xmlAttributes, '>', $NL;
			$this->dumpNode($element[1]);
			echo $inset, '</', $xmlElement, '>', $NL;
		}
	}
	private function dumpSubValue($type, $value) {
		$subDumpMap = array(
			self::TYPE_ARRAY => 'dumpArray',
			self::TYPE_OBJECT => 'dumpObject',
		);

		if (false === array_key_exists($type, $subDumpMap))
			return;

		$this->statePush();

		$method = $subDumpMap[$type];
		$this->$method($value);

		$this->statePop();
	}
	private function dumpValue($type, $value) {
		switch($type) {
			case self::TYPE_ARRAY:
			case self::TYPE_MEMBERS:
				return sprintf(' members="%d"', count($value));
			case self::TYPE_STRING:
				// TODO imagine some propper CDATA for strings
				return sprintf(' len="%d" value="%s"', strlen($value), htmlspecialchars($this->dumpStringNice($value)));
			case self::TYPE_INT:
			case self::TYPE_FLOAT:
				return sprintf(' value="%s"', $value);
			case self::TYPE_OBJECT:
				$count = count($value[1][1]);
				return sprintf(' class="%s" members="%d"', $value[0][1], $count);
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
				throw new \InvalidArgumentException(sprintf('Type %s unknonwn.', $type));
		}
	}
	private function hasInnerElements($type) {
		switch($type) {
			case self::TYPE_ARRAY:
			case self::TYPE_OBJECT:
				return true;
			case self::TYPE_MEMBERS:
				throw new \InvalidArgumentException('TYPE_MEMBERS not wanted for the moment');
			default:
				return false;
		}
	}
	private function dumpNode(array $parsed) {
		list($type, $typeName, $valueValue) = $this->typeExport($parsed);

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
		if (count($parsed) != 2) {
			throw new \InvalidArgumentException('Parameter is expected to be an array of two values.');
		}

		$xmlRoot = $this->config['tags']['root'];

		$doctype = null;

		echo $this->config('declaration', true);
		echo $this->config('doctype', true);

		echo '<', $xmlRoot, '>', $this->config('newline');

		$this->dumpNode($parsed);

		echo '</', $xmlRoot, '>', $this->config('newline');
	}
	/**
	 * print serialized array notation
	 *
	 * @param array $parsed serialized array notation data.
	 * @param array $config (optional) dumper configuration
	 */
	public function dump(array $parsed, array $config=array()) {
		$config && $this->setConfig($config);
		$this->dumpAny($parsed);
	}
}