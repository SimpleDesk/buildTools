<?php
// Stuff we will ignore.
$ignoreFiles = array(
);

/* This is mostly meant for local usage.
   To add additional PHP Binaries, create a check-php-syntax-binaries.txt
   Add in this in each line the binary file, i.e: /usr/bin/php
*/
$addditionalPHPBinaries = array();
if (!empty(dirname(__FILE__) . '/check-php-syntax-binaries.txt'))
	$addditionalPHPBinaries = file(dirname(__FILE__) . '/check-php-syntax-binaries.txt');

$curDir = '.';
if (isset($_SERVER['argv'], $_SERVER['argv'][1]))
	$curDir = $_SERVER['argv'][1];

$foundBad = false;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($curDir, FilesystemIterator::UNIX_PATHS)) as $currentFile => $fileInfo)
{
	// Only check PHP
	if ($fileInfo->getExtension() !== 'php')
		continue;

	foreach ($ignoreFiles as $if)
		if (preg_match('~' . $if . '~i', $currentFile))
			continue 2;

	# Always check against the base.
	$result = trim(shell_exec('php -l ' . $currentFile));

	if (!preg_match('~No syntax errors detected in ' . $currentFile . '~', $result))
	{
		$foundBad = true;
		fwrite(STDERR, 'PHP via $PATH:' . $result . "\n");
		continue;
	}

	// We have additional binaries we want to test against?
	foreach ($addditionalPHPBinaries as $binary)
	{
		$binary = trim($binary);
		$result = trim(shell_exec($binary . ' -l ' . $currentFile));

		if (!preg_match('~No syntax errors detected in ' . $currentFile . '~', $result))
		{
			$foundBad = true;
			fwrite(STDERR, 'PHP via ' . $binary . ':' . $result . "\n");
			continue 2;
		}
	}
}

exit($foundBad ? 1 : 0);