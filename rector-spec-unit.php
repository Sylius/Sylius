<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PhpSpecToPHPUnit\Set\MigrationSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\Visibility\Rector\ClassMethod\ExplicitPublicClassMethodRector;

return static function (RectorConfig $config): void {
    $config->paths([
        __DIR__ . '/src/Sylius/Component/Review/spec',
    ]);

    $config->importNames();
    $config->removeUnusedImports();

    $config->sets([
        LevelSetList::UP_TO_PHP_82,
        MigrationSetList::PHPSPEC_TO_PHPUNIT,
        PHPUnitSetList::PHPUNIT_90,
    ]);
    $config->rules([
        AddParamTypeDeclarationRector::class,
        AddReturnTypeDeclarationRector::class,
        ExplicitPublicClassMethodRector::class,
    ]);
};
