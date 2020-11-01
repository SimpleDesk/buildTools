<?php
/**************************************************************
*          Simple Desk Project - www.simpledesk.net           *
***************************************************************
*       An advanced help desk modification built on SMF       *
***************************************************************
*                                                             *
*         * Copyright 2020 - SimpleDesk.net                   *
*                                                             *
*   This file and its contents are subject to the license     *
*   included with this distribution, license.txt, which       *
*   states that this software is New BSD Licensed.            *
*   Any questions, please contact SimpleDesk.net              *
*                                                             *
***************************************************************
* SimpleDesk Version: 2.1 RC1                                 *
* File Info: check-eof.php                                    *
**************************************************************/

// Stuff we will ignore.
$ignoreFiles = array(
	// Build tools
	'./buildtools/[A-Za-z0-9-_]+.php',
);

// No file? Thats bad.
if (!isset($_SERVER['argv'], $_SERVER['argv'][1]))
	die('Error: No File specified' . "\n");

// The file has to exist.
$currentFile = $_SERVER['argv'][1];
if (!file_exists($currentFile))
	die('Error: File does not exist' . "\n");

// Is this ignored?
foreach ($ignoreFiles as $if)
	if (preg_match('~' . $if . '~i', $currentFile))
		die;

// Less efficent than opening a file with fopen, but we want to be sure to get the right end of the file. file_get_contents
$file = fopen($currentFile, 'r');

// Error?
if ($file === false)
	die('Error: Unable to open file ' . $currentFile . "\n");

// Seek the end minus some bytes.
fseek($file, -100, SEEK_END);
$contents = fread($file, 100);

// There is some white space here.
if (preg_match('~}\s+$~', $contents, $matches))
	die('Error: End of File contains extra spaces in ' . $currentFile . "\n");
// It exists! Leave.
elseif (preg_match('~}$~', $contents, $matches))
	die();

// There is some white space here.
if (preg_match('~\';\s+$~', $contents, $matches))
	die('Error: End of File Strings contains extra spaces in ' . $currentFile . "\n");
// It exists! Leave.
elseif (preg_match('~\';$~', $contents, $matches))
	die();