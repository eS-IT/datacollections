<?php
$classDir       = __DIR__ . '/../Classes/';
$rulesString    = \file_get_contents(__DIR__ . '/php_cs.dist.json');
$rulesArray     = \json_decode($rulesString, true, 512, JSON_THROW_ON_ERROR);

if (isset($rulesArray['fixers']) && \is_array($rulesArray['fixers']) && \count($rulesArray['fixers'])) {
    $finder = PhpCsFixer\Finder::create();
    $finder->exclude('vendor')->in($classDir);

    $config = new PhpCsFixer\Config();
    $config->setRules($rulesArray['fixers'])
           ->setRiskyAllowed(true)
           ->setFinder($finder);

    return $config;
}
