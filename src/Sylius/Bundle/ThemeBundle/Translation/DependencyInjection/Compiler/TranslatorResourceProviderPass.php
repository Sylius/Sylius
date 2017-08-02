<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TranslatorResourceProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $symfonyTranslator = $container->findDefinition('translator.default');
            $syliusResourceProvider = $container->findDefinition('sylius.theme.translation.resource_provider.default');
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $symfonyResourcesFiles = $this->extractResourcesFilesFromSymfonyTranslator($symfonyTranslator);

        $syliusResourceProvider->replaceArgument(0, array_merge(
            $syliusResourceProvider->getArgument(0),
            $symfonyResourcesFiles
        ));
    }

    /**
     * @param Definition $symfonyTranslator
     *
     * @return array
     */
    private function extractResourcesFilesFromSymfonyTranslator(Definition $symfonyTranslator)
    {
        try {
            $options = $symfonyTranslator->getArgument(3);

            if (!array_key_exists('resource_files', $options)) {
                $options = $symfonyTranslator->getArgument(4);
            }
        } catch (OutOfBoundsException $exception) {
            $options = [];
        }

        $languagesFiles = isset($options['resource_files']) ? $options['resource_files'] : [];

        $resourceFiles = [];
        foreach ($languagesFiles as $language => $files) {
            foreach ($files as $file) {
                $resourceFiles[] = $file;
            }
        }

        return $resourceFiles;
    }
}
