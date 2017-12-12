<?php
/**
 * Simple code coverage checker.
 *
 * To run it use:
 *
 * php coverage.php <path to your clover.xml> <minimum code coverage percentage>
 *
 * If the coverage reported by your Clover-formatted report is equal or greater than the value specified,
 * the exit code will be 0, or 1 otherwise.
 */

if ($argc < 2) {
    echo "Missing path to Clover report.".PHP_EOL;
    exit(1);
}

if ($argc < 3) {
    echo "Missing minimum coverage.".PHP_EOL;
    exit(1);
}

$file = $argv[1];
$minCoverage = $argv[2];
if (!file_exists($file)) {
    die("File '{$file}' not found.");
}

$xml = new SimpleXMLElement(file_get_contents($file));
$metrics = $xml->project->metrics[0];

$tpc = ($metrics['coveredelements'] + $metrics['coveredmethods']) / ($metrics['elements'] + $metrics['methods']);

$hasMinimumCoverage = $tpc >= $minCoverage / 100;

$prettyTpc = round($tpc * 100, 2);
if ($hasMinimumCoverage) {
    printf('SUCCESS: Coverage is %s.'.PHP_EOL, $prettyTpc);
    exit(0);
} else {
    printf('ERROR: Coverage is %s'.PHP_EOL, $prettyTpc);
    exit(1);
}
