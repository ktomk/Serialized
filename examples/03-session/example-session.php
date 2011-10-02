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
 * @package Examples
 */

Namespace Serialized;

require_once(__DIR__.'/../../src/Serialized.php');

/**
 * ouput some information about session configuration and
 * files on the local system first for information purposes.
 */
print "\nYour Session configuration:\n\n";
foreach(array('serialize_handler', 'save_path', 'save_handler', ) as $setting) {
	$$setting = ini_get('session.'.$setting);
	printf("  %'.-20s: %s\n", $setting, $$setting);
}
if ($save_handler === 'files') {
	// NOTE: does not handle session subdirs
	$files = glob(sprintf('%s/sess_*', $save_path), GLOB_NOSORT||GLOB_BRACE);
	$num = count($files);
	printf("\nThere are %d session(s) stored in \"%s\", listing 15 max:\n", $num, $save_path);
	$count = 0;
	foreach($files as $file) {
		if (++$count > 14) break;
		printf("  #%02d: %s\n", $count, basename($file));
	}
}
print "\n";

/**
 * make use of example session data in the
 * session_files subdirectory and show how
 * it parses and dumps the data
 */

$files = glob(__DIR__.'/session_files/sess_*', GLOB_NOSORT);
if (!$files) {
	// @codeCoverageIgnoreStart
	throw new \UnexpectedValueException('Found no session files in "session_files" directory');
	// @codeCoverageIgnoreEnd
}

print "Parsing example Session Data:\n";
$mode = 0;
foreach($files as $file) {
	$serializedSession = file_get_contents($file);
	$parser = new SessionParser($serializedSession);
	$dumpMode = $mode++%2?'text':'xml';
	printf("\nSession \"%s\" as %s:\n", basename($file), $dumpMode);
	$parser->dump($dumpMode);
	print "\n";
}