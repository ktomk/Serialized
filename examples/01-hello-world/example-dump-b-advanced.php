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
 * @version 0.2.5
 * @package Examples
 */

Namespace Serialized\Example02;
Use Serialized\Parser;

require_once(__DIR__.'/../../src/Serialized.php');

class parentClass {
	private $privee = 'parent';
	public function getPrivee() {
		return $this->privee;
	}
}

class exampleClass extends parentClass {
	public function __construct() {
		$this->getPrivee();
	}
	protected $str = 'test';
}

$object = new exampleClass();
$object->array = array();
$object->test = null;

$serialized = serialize($object);
$parser = new Parser($serialized);
$parser->dump();
