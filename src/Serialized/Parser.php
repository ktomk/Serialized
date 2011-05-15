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
 * @version 0.2.3
 * @package Serialized
 */

Namespace Serialized;
Use \OutOfRangeException;
Use \InvalidArgumentException;
Use \UnexpectedValueException;

/**
 * Serialize Parser
 */
class Parser implements Value, ValueTypes {
	/**
	 * @var string serialized
	 */
	protected $data = '';
	public function __construct($serialized = 'N;') {
		$this->setSerialized($serialized);
	}
	/**
	 * @return string datatype
	 */
	public function getType() {
		$parsed = $this->getParsed();
		return $parsed[0];
	}
	public function getSerialized() {
		return $this->data;
	}
	public function setSerialized($serialized) {
		$this->data = (string) $serialized;
	}
	/**
	 * @return array(int type, int byte length)
	 */
	private function lookupVartype($offset) {
		$serialized = $this->data;
		$len = strlen($serialized) - $offset;
		$error = array(self::TYPE_INVALID, 0);
		if ($len < 2) return $error;
		# NULL; fixed length: 2
		$token = $serialized[$offset];
		$test = $serialized[$offset+1];
		if ('N' === $token && ';' === $test)
			return array(self::TYPE_NULL, 0);
		if (':' !== $test)
			return $error;
		if (false === strpos('abCdiOrRsS', $token))
			return $error;
		return array(TypeChars::by($token), 2);
	}
	private function extract($offset) {
		$delta = 12;
		$start = max(0, $offset-$delta);
		$before = $offset - $start;
		$end = min(strlen($this->data), $offset + $delta + 1);
		$after = $end - $offset;
		$end = $end - $after + 1;
		$build = '';
		$build .= ($before === $delta ? '...' : '');
		$build .= substr($this->data, $start, $before);
		$build .= isset($this->data[$offset]) ? sprintf('[%s]', $this->data[$offset]) : sprintf('<-- #%d', strlen($this->data)-1);
		$build .= substr($this->data, $end, $after);
		$build .= ($after === $delta ? '...' : '');

		return $build;
	}
	/**
	 * @param string $pattern
	 * @param int $offset
	 * @return int length in chars of match
	 */
	protected function matchRegex($pattern, $offset) {
		$return = 0;
		$subject = $this->data;
		if (!isset($subject[$offset])) {
			throw new ParseException(sprintf('Illegal offset #%d ("%s") for pattern, length is #%d.', $offset, $this->extract($offset), strlen($subject)));
		}
		$found = preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, $offset);
		if (false === $found) {
			// @codeCoverageIgnoreStart
			$error = preg_last_error();
			throw new UnexpectedValueException(sprintf('Regular expression ("%s") failed (Error-Code: %d).', $pattern, $error));
			// @codeCoverageIgnoreEnd
		}
		$found
			&& isset($matches[0][1])
			&& $matches[0][1] === $offset
			&& $return = strlen($matches[0][0])
		;
		return $return;
	}
	/**
	 * @param string $pattern
	 * @param int $offset
	 * @throws ParseException
	 * @return string
	 */
	private function parseRegex($pattern, $offset) {
		$match = $this->matchRegex($pattern, $offset);
		if (!$match) {
			throw new ParseException(sprintf('Invalid character sequence for %s at offset #%d ("%s").', $pattern, $offset, $this->extract($offset)));
		}
		return substr($this->data, $offset, $match);
	}
	protected function expectChar($charExpected, $offset) {
		if (!isset($this->data[$offset])) {
			throw new ParseException(sprintf('Unexpected EOF, expected Expected "%s". At offset #%d ("%s").', $charExpected, $offset, $this->extract($offset)));
		}
		$char = $this->data[$offset];
		if ($charExpected !== $char) {
			throw new ParseException(sprintf('Unexpected char "%s", expected "%s". At offset #%d ("%s").', $char, $charExpected, $offset, $this->extract($offset)));
		}
	}
	protected function expectEof($offset) {
		$len = strlen($this->data);
		$end = ($offset + 1) === $len;
		if (!$end) {
			throw new ParseException(sprintf('Not EOF after offset #%d ("%s"). Length is %d.', $offset, $this->extract($offset), $len));
		}
	}
	private function parseRecursionValue($offset) {
		$len = $this->matchRegex('([1-9]+[0-9]*)', $offset);
		if (!$len) {
			throw new ParseException(sprintf('Invalid character sequence for recursion index at offset %d.', $offset));
		}
		$this->expectChar(';', $offset+$len);
		$valueString = substr($this->data, $offset, $len);
		$value = (int) $valueString;
		return array($value, $len+1);
	}
	private function parseRecursionrefValue($offset) {
		return $this->parseRecursionValue($offset);
	}
	private function parseStringValue($offset, $terminator = ';') {
		$len = $this->parseRegex('([+]?[0-9]+:")', $offset);
		$lenLen = strlen($len);
		$lenInt = (int) $len;
		$this->expectChar('"', $offset+$lenLen+$lenInt);
		$this->expectChar($terminator, $offset+$lenLen+$lenInt+1);
		$value = substr($this->data, $offset+$lenLen, $lenInt);
		return array($value, $lenLen+$lenInt+2);
	}
	private function unserializeString($offset, $len, &$consume) {
		$string = '';
		$consume = 0;
		$subject = $this->data;
		$pos = $offset;
		for ($i = 0; $i < $len; $i++) {
			if (!isset($subject[$pos])) {
				throw new ParseException(sprintf('Unexpected EOF at #%d ("%s")', $pos, $this->extract($pos)));
			}
			$char = $subject[$pos];
			if ($char === '\\') {
				$token = $this->parseRegex('([0-9a-fA-F]{2})', $pos+1);
				$char = chr(hexdec($token));
				$pos+=2;
			}
			$string .= $char;
			$pos++;
		}
		$consume = $pos - $offset;
		return $string;
	}
	private function parseStringEncodedValue($offset) {
		$len = $this->parseRegex('([+]?[0-9]+:")', $offset);
		$lenLen = strlen($len);
		$lenInt = (int) $len;
		if ($offset+$lenLen+$lenInt>strlen($this->data)) {
			throw new ParseException(sprintf('String length %d too large for data at offset #%d ("%s").', $lenInt, $offset, $this->extract($offset)));
		}
		$consume = 0;
		$string = $this->unserializeString($offset+$lenLen, $lenInt, $consume);
		$this->expectChar('"', $offset+$lenLen+$consume);
		$this->expectChar(';', $offset+$lenLen+$consume+1);
		return array($string, $lenLen+$consume+2);
	}
	private function parseIntValue($offset) {
		$len = $this->matchRegex('([-+]?[0-9]+)', $offset);
		if (!$len) {
			throw new ParseException(sprintf('Invalid character sequence for integer value at offset %d.', $offset));
		}
		$this->expectChar(';', $offset+$len);
		$valueString = substr($this->data, $offset, $len);
		$value = (int) $valueString;
		return array($value, $len+1);
	}
	private function parseInvalidValue($offset) {
		throw new ParseException(sprintf('Invalid ("%s") at offset %d.', $this->extract($offset), $offset));
	}
	private function parseFloatValue($offset) {
		$pattern = '((?:[-]?INF|[+-]?(?:(?:[0-9]+|(?:[0-9]*[\.][0-9]+)|(?:[0-9]+[\.][0-9]*))|(?:[0-9]+|(?:([0-9]*[\.][0-9]+)|(?:[0-9]+[\.][0-9]*)))[eE][+-]?[0-9]+));)';
		$len = $this->matchRegex($pattern, $offset);
		if (!$len) {
			throw new ParseException(sprintf('Invalid character sequence for float vartype at offset %d.', $offset));
		}
		$valueString = substr($this->data, $offset, $len-1);
		$value = unserialize("d:{$valueString};"); // using unserialize for INF and -INF.
		return array($value, $len);
	}
	private function parseNullValue($offset) {
		$this->expectChar('N', $offset);
		$this->expectChar(';', $offset+1);
		return array(null, 2);
	}
	private function parseBoolValue($offset) {
		$char = $this->data[$offset];
		if ('0' !== $char && '1' !== $char) {
			throw new ParseException(sprintf('Unexpected char "%s" at offset %d. Expected "0" or "1".', $char, $offset));
		}
		$this->expectChar(';',$offset+1);
		$valueInt = (int) $char;
		$value = (bool) $valueInt;
		return array($value, 2);
	}
	private function parseCustomValue($offset) {
		list($className, $classLen) = $this->parseStringValue($offset, ':');
		$dataLenLength = $this->matchRegex('([0-9]+(?=:))', $offset+$classLen);
		if (!$dataLenLength) {
			throw new ParseException(sprintf('Invalid character sequence for custom vartype at offset %d.', $offset+$classLen));
		}
		$dataLengthString = substr($this->data, $offset+$classLen, $dataLenLength);
		$dataLength = (int) $dataLengthString;
		$this->expectChar('{', $offset+$classLen+1+$dataLenLength);
		$this->expectChar('}', $offset+$classLen+1+$dataLenLength+1+$dataLength);
		$data = $dataLength ? substr($this->data, $offset+$classLen+1+$dataLenLength+1, $dataLength) : '';
		$value = array(
			array(TypeNames::of(self::TYPE_CLASSNAME), $className),
			array(TypeNames::of(self::TYPE_CUSTOMDATA), $data)
		);
		$consume = $classLen+$dataLenLength+2+$dataLength+1;
		return array($value, $consume);
	}
	private function invalidArrayKeyType($type) {
		return !in_array($type, array('int', 'string'));
	}
	private function parseArrayValue($offset) {
		$offsetStart = $offset;
		$lenString = $this->parseRegex('([+]?[0-9]+:{)', $offset);
		$lenMatch = strlen($lenString);
		$lenLen = (int) $lenString;
		$offset += $lenMatch;
		$value = array();
		for($elementNumber=0; $elementNumber<$lenLen; $elementNumber++) {
			list($keyHinted, $keyLength) = $this->parseValue($offset);
			list($keyTypeName) = $keyHinted;
			if ($this->invalidArrayKeyType($keyTypeName)) {
				throw new ParseException(sprintf('Invalid vartype %s (%d) for array key at offset #%d ("%s").', $keyTypeName, TypeNames::by($keyTypeName), $offset, $this->extract($offset)));
			}
			list($valueHinted, $valueLength) = $this->parseValue($offset+=$keyLength);
			$offset+=$valueLength;
			$element = array(
				$keyHinted,
				$valueHinted,
			);
			$value[] = $element;
		}
		$this->expectChar('}', $offset);
		$len = $offset-$offsetStart+1;
		return array($value, $len);
	}
	private function parseObjectValue($offset) {
		$totalLen = 0;
		list($className, $len) = $this->parseStringValue($offset, ':');
		$totalLen += $len;
		list($classMembers, $len) = $this->parseArrayValue($offset+$len);
		foreach($classMembers as $index => $member) {
			list(list($typeSpec)) = $member;
			if ('string' !== $typeSpec)
				throw new ParseException(sprintf('Unexpected type %s, expected string on offset #%d ("%s").', $typeSpec, $offset, $this->extract($offset)));
			$classMembers[$index][0][0] = TypeNames::of(self::TYPE_MEMBER);
		}
		$totalLen += $len;

		$count = count($classMembers);
		$value = array(array(TypeNames::of(self::TYPE_CLASSNAME), $className), array(TypeNames::of(self::TYPE_MEMBERS), $classMembers));
		return array($value, $totalLen);
	}
	/**
	 * parse for a serialized value at offset
	 *
	 * @param int $offset byte offset
	 * @return array array notation of serialized value
	 * @throws ParseException
	 */
	public function parseValue($offset) {
		list($type, $consume) = $this->lookupVartype($offset);
		$typeName = TypeNames::of($type);
		$function = sprintf('parse%sValue', ucfirst($typeName));
		if (!is_callable(array($this, $function))) {
			// @codeCoverageIgnoreStart
			throw new UnexpectedValueException(sprintf('Unable to parse vartype %s (%d) at offset %s. Parsing function %s is not callable', $typeName, $type, $offset, $function));
			// @codeCoverageIgnoreEnd
		}
		list($value, $len) = $this->$function($offset+$consume);
		// parse encoded strings as strings (php forward compatiblitity)
		if ($type === self::TYPE_STRINGENCODED) {
			$typeName = TypeNames::of(self::TYPE_STRING);
		}
		$hinted = array($typeName, $value);
		return array($hinted, $len+$consume);
	}
	public function getParsed() {
		list($value, $len) = $this->parseValue(0);
		$this->expectEof($len-1);
		return $value;
	}
	/**
	 * parse string of serialized  data into array notation
	 *
	 * @param string $serialized
	 * @return array|false array notation, false on parse error
	 */
	public static function parse($serialized) {
		$parser = new self($serialized);
		try {
			$result = $parser->getParsed();
		} catch (ParseException $e) {
			trigger_error(sprintf('Error parsing serialized string: %s', $e->getMessage()), E_USER_WARNING);
			$result = false;
		}
		return $result;
	}
	/**
	 * print serialized array notation
	 *
	 * @param string $type (optional) dumper type / format (Text, XML, Serialized)
	 * @param array $config (optional) dumper configuration
	 */
	public function dump($type = null, array $config = array()) {
		$parsed = $this->getParsed();
		$dumper = Dumper::factory($type, $config);
		$dumper->dump($parsed);
	}
	/**
	 * get dump of a serialized array notation
	 *
	 * @param string $type (optional) dumper type / format (Text, XML, Serialized)
	 * @param array $config (optional) dumper configuration
	 * @return string dump
	 */
	public function getDump($type = null, array $config = array()) {
		$parsed = $this->getParsed();
		$dumper = Dumper::factory($type, $config);
		return $dumper->getDump($parsed);
	}
}