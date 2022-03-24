<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @see https://github.com/symfony/symfony/issues/45517
 */
class DisablePhpstanPropertyExtractorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('property_info.phpstan_extractor')) {
            $container->removeDefinition('property_info.phpstan_extractor');
            $def = $container->getDefinition('property_info');
            /** @var IteratorArgument $typeExtractors */
            $typeExtractors = $def->getArgument(1);
            $newExtractors = [];

            foreach ($typeExtractors->getValues() as $extractor) {
                $extractorId = (string) $extractor;
                if ('property_info.phpstan_extractor' !== $extractorId) {
                    $newExtractors[] = $extractor;
                }
            }

            $def->setArgument(1, new IteratorArgument($newExtractors));
        }
    }
}
