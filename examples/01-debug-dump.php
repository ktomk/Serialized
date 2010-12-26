<?php
/**
 * Serialized - PHP Library for Serialized Data
 * 
 * Copyright (C) 2010  Tom Klingenberg
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
 * @version 0.0.0
 * @package Examples
 */

  Namespace Serialized;

  require_once(__DIR__.'/../src/Serialized.php');
  
  $data = array(
  	'foo' => 1,
  	'bar' => range(2,20,3),
    'baz' => new Parser
  );
  
  $serialized = serialize($data);

  $parser = new Parser($serialized);

  $parser->dump();
  
  // on array notation
  $parser->dump(
  	Parser::parse(
  		serialize(
  			range(1,10)
  		)
  	)
  );