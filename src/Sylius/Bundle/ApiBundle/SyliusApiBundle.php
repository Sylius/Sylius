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

namespace Sylius\Bundle\ApiBundle;

use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\CommandDataTransformerPass;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\ExtractorMergingCompilerPass;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\FlattenExceptionNormalizerDecoratorCompilerPass;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\LegacyErrorHandlingCompilerPass;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\SyliusPriceHistoryLegacyAliasesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusApiBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CommandDataTransformerPass());
        $container->addCompilerPass(new FlattenExceptionNormalizerDecoratorCompilerPass());
        $container->addCompilerPass(new LegacyErrorHandlingCompilerPass());
        $container->addCompilerPass(new SyliusPriceHistoryLegacyAliasesPass());
        $container->addCompilerPass(new ExtractorMergingCompilerPass());
    }
}
