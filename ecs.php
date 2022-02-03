<?php

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');

//    $containerConfigurator->services()->set(HeaderCommentFixer::class)->call('configure', [
//        'location' => 'after_open',
//        'header' =>
//'This file is part of the Sylius package.
//
//(c) Paweł Jędrzejewski
//
//For the full copyright and license information, please view the LICENSE
//file that was distributed with this source code.',
//    ]);

    $containerConfigurator->parameters()->set(Option::SKIP, [
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        InlineDocCommentDeclarationSniff::class . '.NoAssignment',
        VisibilityRequiredFixer::class => ['*Spec.php'],
        '**/var/*',
    ]);
};
