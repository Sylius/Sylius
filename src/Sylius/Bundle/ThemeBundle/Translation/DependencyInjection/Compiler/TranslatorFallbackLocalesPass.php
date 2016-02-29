<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslatorFallbackLocalesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $symfonyTranslator = $container->findDefinition('translator.default');
            $syliusTranslator = $container->findDefinition('sylius.theme.translation.translator');
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $methodCalls = array_filter($symfonyTranslator->getMethodCalls(), function (array $methodCall) {
            return 'setFallbackLocales' === $methodCall[0];
        });

        foreach ($methodCalls as $methodCall) {
            $syliusTranslator->addMethodCall($methodCall[0], $methodCall[1]);
        }
    }
}
