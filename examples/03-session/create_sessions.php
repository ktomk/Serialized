<?php
/**
 * creates a session, for playing purposes
 */

session_start();

$_SESSION['test'] = 1;
$_SESSION['more'] = array(56, 'key' => 57);
$_SESSION['again'] = 2;
