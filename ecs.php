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

use PhpCsFixer\Fixer\ClassNotation\OrderedTypesFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import('vendor/sylius-labs/coding-standard/ecs.php');

    $config->parallel();
    $config->paths(['src/Sylius', 'tests']);
    $config->skip([
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        InlineDocCommentDeclarationSniff::class . '.NoAssignment',
        VisibilityRequiredFixer::class => ['*Spec.php'],
        ErrorSuppressionFixer::class => 'src/Sylius/Bundle/CoreBundle/DependencyInjection/Compiler/BackwardsCompatibility/CancelOrderStateMachineCallbackPass.php',
        '**/var/*',
    ]);
    $config->ruleWithConfiguration(PhpdocSeparationFixer::class, ['groups' => [['Given', 'When', 'Then']]]);
    $config->ruleWithConfiguration(OrderedTypesFixer::class, ['null_adjustment' => 'always_last']);
    $config->ruleWithConfiguration(NullableTypeDeclarationForDefaultNullValueFixer::class, ['use_nullable_type_declaration' => true]);
    $config->ruleWithConfiguration(
        HeaderCommentFixer::class,
        [
            'location' => 'after_open',
            'comment_type' => HeaderCommentFixer::HEADER_COMMENT,
            'header' => <<<TEXT
This file is part of the Sylius package.

(c) Sylius Sp. z o.o.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
TEXT
        ]
    );
};
