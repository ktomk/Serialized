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

$config = array();

$config['user'][0]['id'][] = 0;
$config['user'][0]['name'][] = 'John Doe';
$config['user'][0]['pass'][] = 'ncveohf09o&%$m(i/8';
$config['user'][1]['id'][] = 1;
$config['user'][1]['name'][] = 'Max Pd';
$config['user'][1]['pass'][] = 'f824-c3948.309';

return array(serialize($config));
