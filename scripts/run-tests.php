<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Process\Process;

$projectRoot = dirname(__DIR__);
$testsRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . 'tests');
$phpunit = $projectRoot . DIRECTORY_SEPARATOR . 'vendor/phpunit/phpunit/phpunit';

$command = [
    PHP_BINARY,
    $phpunit,
    '--configuration',
    $projectRoot . DIRECTORY_SEPARATOR . 'phpunit.xml',
];

if (isset($argv[1])) {
    $relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $argv[1]);
    $testPath = realpath($testsRoot . DIRECTORY_SEPARATOR . $relativePath);
    $testsRootPrefix = $testsRoot . DIRECTORY_SEPARATOR;

    if ($testPath === false || !str_starts_with($testPath, $testsRootPrefix) || !is_file($testPath)) {
        fwrite(STDERR, "Test file not found under the tests directory: {$argv[1]}\n");
        exit(2);
    }

    $command[] = $testPath;
}

$process = new Process($command, $projectRoot);
$process->setTimeout(null);
$exitCode = $process->run(static function (string $type, string $buffer): void {
    echo $buffer;
});

exit($exitCode);
