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

Namespace Serialized;
Use \Exception;

/**
 * Serialize Dumper
 *
 * @todo solve typeByName() code duplication (taken from Parser)
 */
class Dumper implements ValueTypes {
	/**
	 * configuration local store
	 * @var array
	 */
	protected $config = array();

	/**
	 * dumper state
	 * @var stdClass
	 */
	protected $state;
	/**
	 * stack of states
	 * @var array
	 */
	private $stack = array();
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
	private function stateInit() {
		$state = new \stdClass();
		$state->level = 0;
		$state->inset = '';
		$this->state = $state;
	}
	public function __construct() {
		$this->stateInit();
	}
	/**
	 * push the current state onto the stack
	 */
	protected function statePush() {
		array_push($this->stack, clone $this->state);
		$this->state->level++;
	}
	/**
	 * pop state from stack
	 */
	protected function statePop() {
		$this->state = array_pop($this->stack);
	}
	protected function typeByName($name) {
		$map = array_flip($this->typeNames);
		if (!isset($map[$name])) {
			throw new \InvalidArgumentException(sprintf('Unknown name "%s" to identify a vartype.', $name));
		}
		return $map[$name];
	}
	protected function typeExport(array $parsed) {
		return array_merge((array) $this->typeByName($parsed[0]), $parsed);
	}
	/**
	 * utility function
	 *
	 * @param string $string String ;)
	 * @return string Nice String ;)
	 */
	protected function dumpStringNice($string) {
		static $seq = array(0x09 => 't', 0x0A => 'n', 0x0B => 'v', 0x0C => 'f',  0x0D => 'r');
		for(
		    $r = '',
		    $l = strlen($string),
		    $i = 0
		    ;
		    $i < $l
		    ;
		    $c = $string[$i++],
		    $o = ord($c),
		    ($f = 0x08 < $o && $o < 0x0E) && $c = $seq[$o],
		    ($b = $f || (0x1F < $o && $o < 0x7F)) && ($f || 0x22 === $o || 0x24 === $o || 0x5C === $o) && $c = '\\'.$c,
		    $r.= $b ? $c : '\x'.strtoupper(substr('0'.dechex($o),-2))
		);
		return $r;
	}
	/**
	 * config/ini (n-depth) array_merge
	 *
	 * config is an array without numerical keys and with an n-depth but
	 *
	 * if a non-array is to be set to an array, it will fail. the definition
	 * is by default ($source), overwriters (sub or superset of $add) will get killed.
	 */
	protected function config_merge_deep(array $source, array $add, $noticeUndefined = true) {
		static $base = '';
		foreach ($add as $key => $value) {
			$path = $base.'/'.$key;
			if (is_int($key)) {
				continue;
			}
			if (true === is_array($value)) {
				$value = $this->config_merge_deep(array(), $value, false); // merge with yourself, will trigger lot of errors
			}
			if (!array_key_exists($key, $source)) {
				if ($noticeUndefined ) {
					trigger_error(sprintf('Configuration "%s" was not defined.', $path), E_USER_NOTICE);
				}
				$source[$key] = $value;
				continue;
			}
			if (!is_array($source[$key]) && !is_array($value)) {
				$source[$key] = $value;
				continue;
			}
			if (!is_array($source[$key]) && is_array($value)) {
				trigger_error(sprintf('Can not merge array (key: "%s") into a non-array config entry.', $key), E_USER_WARNING);
				continue;
			}
			if (is_array($source[$key]) && !is_array($value)) {
				trigger_error(sprintf('Can not overwrite existing array (key: "%s") with a value ("%s").', $key, $value), E_USER_WARNING);
				continue;
			}
			list($save, $base) = array($base, $path);
			$source[$key] = $this->config_merge_deep($source[$key], $value);
			$base = $save;
		}
		return $source;
	}
	public function setConfig(array $config) {
		$this->config = $this->config_merge_deep($this->config, $config);
	}
	private function dumpAs($type, array $parsed, array $config) {
		$class = sprintf('%s\Dumper\%s', __NAMESPACE__, ucfirst(strtolower($type)));
		$dumper = new $class();
		$config && $dumper->setConfig($config);
		$dumper->dump($parsed);
	}
	/**
	 * print serialized array notation
	 *
	 * @param array $parsed serialized array notation data.
	 */
	public function dump(array $parsed, array $config = array()) {
		$this->dumpAs('text', $parsed,  $config);
	}
}