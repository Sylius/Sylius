<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PhpSpecToPHPUnit\Set\MigrationSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\Visibility\Rector\ClassMethod\ExplicitPublicClassMethodRector;

return RectorConfig::configure()
    ->withPaths([
        // __DIR__ . '/src/Sylius/Bundle/AddressingBundle/spec',
    ])
    ->withImportNames(removeUnusedImports: true)
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
        MigrationSetList::PHPSPEC_TO_PHPUNIT,
        PHPUnitSetList::PHPUNIT_100,
    ])
    ->withRules([
        AddParamTypeDeclarationRector::class,
        AddReturnTypeDeclarationRector::class,
        ExplicitPublicClassMethodRector::class,
    ])

    // After executing:
    // vendor/bin/rector process -c rector-spec-unit.php
    //
    // Some remaining adjustments are required (MacOS sed, other can simply use "sed -i" not "sed -i ''"):
    //
    // # Fix MockObject phpdoc
    // find src/Sylius/path_to_spec_folder/spec/ -type f -name "*Spec.php" -exec sed -i '' "s/\|MockObject /\&MockObject /g" {} +
    //
    // vendor/bin/phpspec-to-phpunit rename-suffix src/Sylius/path_to_spec_folder/spec/
    // vendor/bin/ecs check src/Sylius/path_to_spec_folder/spec/ --fix
    // mv src/Sylius/path_to_spec_folder/spec/ src/Sylius/path_to_spec_folder/Tests/
    // vendor/bin/phpstan analyse
    // vendor/bin/phpunit src/Sylius/path_to_bundle_or_component
    //
    // Finally, you will have to check for non setup 'setUp' method and add it manually to instantiate the missing context property.
    // Ex add this:
    //
    //    private TheCurrentTestedClass $currentClass;
    //
    //    protected function setUp(): void
    //    {
    //        $this->currentClass = new TheCurrentTestedClass();
    //    }
;
