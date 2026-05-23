<?php

declare(strict_types=1);

$cloverPath = $argv[1] ?? null;

if ($cloverPath === null || !is_file($cloverPath)) {
    fwrite(STDERR, "Usage: php tests/check-coverage.php <clover.xml>\n");
    exit(1);
}

$coverage = simplexml_load_file($cloverPath);

if ($coverage === false) {
    fwrite(STDERR, "Unable to read coverage report: {$cloverPath}\n");
    exit(1);
}

$metrics = $coverage->project->metrics;
$coveredStatements = (int) $metrics['coveredstatements'];
$statements = (int) $metrics['statements'];
$percentage = $statements === 0 ? 100.0 : ($coveredStatements / $statements) * 100;

if ($percentage < 100.0) {
    fwrite(
        STDERR,
        sprintf("Code coverage is %.2f%%, expected 100.00%%.\n", $percentage),
    );
    exit(1);
}

fwrite(STDOUT, "Code coverage is 100.00%.\n");
