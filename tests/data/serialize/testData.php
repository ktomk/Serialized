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
 * @package Tests
 */

return array(
	'0001' => 'N;',
	'0002' => 'C:3:"any":2:{N;}',
	'0003' => 'C:3:"any":0:{}',
	'1001' => 'a:4:{s:4:"user";s:9:"user-name";s:7:"network";a:1:{s:7:"localip";s:7:"1.2.3.4";}i:2;s:4:"Zwei";s:8:"language";s:6:"german";}',
	'1002' => 'O:8:"stdClass":6:{s:8:"property";s:4:"test";s:5:"float";d:1;s:4:"bool";b:1;s:4:"null";N;s:9:"recursion";r:1;s:12:"recursionref";R:1;}',
	'1003' => 'O:33:"Serialized\Dumper\testObjectChild":7:{s:37:"'."\x00".'Serialized\Dumper\testObjectChild'."\x00".'ca";s:7:"private";s:5:"'."\x00*\x00".'cb";s:9:"protected";s:2:"cc";s:6:"public";s:38:"'."\x00".'Serialized\Dumper\testObjectParent'."\x00".'pa";s:15:"private, parent";s:5:"'."\x00*\x00".'pb";s:17:"protected, parent";s:2:"pc";s:14:"public, parent";s:46:"'."\x00".'Serialized\Dumper\testÉncödïng'."\x00".'Éncödïng";b:1;}',
	'session-01' => array('test|i:1;more|a:2:{i:0;i:56;s:3:"key";i:57;}again|i:2;'),
	'basic-s-pluslen' => 's:+1:"a";',
	'basic-a-pluslen' => 'a:+0:{}',
	'basic-O-pluslen' => 'O:+8:"stdClass":+0:{}',
	'basic-S-empty' => 'S:+0:"";',
	'basic-S-init' => 'S:3:"\61b\63";',
	// @todo 'basic-s-minlen' => 's:1:"a"',
	// @todo 'basic-s-overlen' => 's:1:"a" - please leave a message after the beep.',
);