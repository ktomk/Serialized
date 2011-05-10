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
 * @package Tests
 */

Namespace Serialized\Dumper;

/**
 * classes with various properties/features to
 * test.
 */

class testÉncödïng {
	private $Éncödïng = true;
	public function getÉncödïng() {
		return $this->Éncödïng;
	}
}

class testObjectParent extends testÉncödïng {
	private $pa = 'private, parent';
	protected $pb = 'protected, parent';
	public $pc = 'public, parent';
	public function getPa() {
		return $this->pa;
	}
}

class testObjectChild extends testObjectParent {
	private $ca = 'private';
	protected $cb = 'protected';
	public $cc = 'public';
	public function getCa() {
		return $this->ca;
	}
}