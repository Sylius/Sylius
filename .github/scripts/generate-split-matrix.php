<?php

declare(strict_types=1);

/**
 * Generates a JSON matrix of packages to split from src/Sylius/*
 * Excludes Behat directory
 *
 * Output format:
 * [
 *   {"directory": "src/Sylius/Component/Order", "repository": "Order"},
 *   {"directory": "src/Sylius/Bundle/CoreBundle", "repository": "SyliusCoreBundle"},
 *   {"directory": "src/Sylius/Abstraction/StateMachine", "repository": "StateMachineAbstraction"},
 *   ...
 * ]
 */

$packages = [];
$basePath = __DIR__ . '/../../src/Sylius';

// Components: src/Sylius/Component/{Name} → Sylius/{Name}
$componentPath = $basePath . '/Component';
if (is_dir($componentPath)) {
    foreach (new DirectoryIterator($componentPath) as $dir) {
        if ($dir->isDot() || !$dir->isDir()) {
            continue;
        }

        $name = $dir->getFilename();
        $packages[] = [
            'directory' => 'src/Sylius/Component/' . $name,
            'repository' => $name,
        ];
    }
}

// Bundles: src/Sylius/Bundle/{Name}Bundle → Sylius/Sylius{Name}Bundle
$bundlePath = $basePath . '/Bundle';
if (is_dir($bundlePath)) {
    foreach (new DirectoryIterator($bundlePath) as $dir) {
        if ($dir->isDot() || !$dir->isDir()) {
            continue;
        }

        $name = $dir->getFilename();
        $packages[] = [
            'directory' => 'src/Sylius/Bundle/' . $name,
            'repository' => 'Sylius' . $name,
        ];
    }
}

// Abstractions: src/Sylius/Abstraction/{Name} → Sylius/{Name}Abstraction
$abstractionPath = $basePath . '/Abstraction';
if (is_dir($abstractionPath)) {
    foreach (new DirectoryIterator($abstractionPath) as $dir) {
        if ($dir->isDot() || !$dir->isDir()) {
            continue;
        }

        $name = $dir->getFilename();
        $packages[] = [
            'directory' => 'src/Sylius/Abstraction/' . $name,
            'repository' => $name . 'Abstraction',
        ];
    }
}

usort($packages, fn ($a, $b) => $a['repository'] <=> $b['repository']);

echo json_encode($packages, JSON_UNESCAPED_SLASHES);
