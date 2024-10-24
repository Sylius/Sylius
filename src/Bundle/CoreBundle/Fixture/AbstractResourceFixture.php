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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractResourceFixture implements FixtureInterface
{
    private OptionsResolver $optionsResolver;

    public function __construct(private ObjectManager $objectManager, private ExampleFactoryInterface $exampleFactory)
    {
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('random', 0)
                ->setAllowedTypes('random', 'int')
                ->setDefault('prototype', [])
                ->setAllowedTypes('prototype', 'array')
                ->setDefault('custom', [])
                ->setAllowedTypes('custom', 'array')
                ->setNormalizer('custom', function (Options $options, array $custom) {
                    if ($options['random'] <= 0) {
                        return $custom;
                    }

                    return array_merge($custom, array_fill(0, $options['random'], $options['prototype']));
                })
        ;
    }

    final public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $i = 0;
        foreach ($options['custom'] as $resourceOptions) {
            $resource = $this->exampleFactory->create($resourceOptions);

            $this->objectManager->persist($resource);

            ++$i;

            if (0 === ($i % 10)) {
                $this->objectManager->flush();
                $this->objectManager->clear();
            }
        }

        $this->objectManager->flush();
        $this->objectManager->clear();
    }

    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->getName());

        /** @var ArrayNodeDefinition $optionsNode */
        $optionsNode = $treeBuilder->getRootNode();

        $optionsNode->children()
            ->integerNode('random')->min(0)->defaultValue(0)->end()
            ->variableNode('prototype')->end()
        ;

        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode('custom');

        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode->requiresAtLeastOneElement()->arrayPrototype();
        $this->configureResourceNode($resourceNode);

        return $treeBuilder;
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        // empty
    }
}
