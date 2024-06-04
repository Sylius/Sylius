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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonFixture extends AbstractResourceFixture
{
    public function __construct(private ObjectManager $objectManager, private ExampleFactoryInterface $exampleFactory)
    {
        parent::__construct($objectManager, $exampleFactory);
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

                    $depth = 8;

                    function generateTaxonData($prefix, $level) {
                        return [
                            'code' => "{$prefix}_{$level}",
                            'translations' => [
                                'en_US' => [
                                    'name' => "Taxon {$prefix} Level {$level}",
                                    'slug' => "taxon-{$prefix}-level-{$level}"
                                ]
                            ]
                        ];
                    }

                    function generateNestedArray($prefix, $depth, $currentDepth = 1) {
                        $data = generateTaxonData($prefix, $currentDepth);
                        if ($currentDepth < $depth) {
                            $children = [];
                            for ($i = 1; $i <= rand(1, $currentDepth); $i++) {
                                $children[] = generateNestedArray("{$prefix}_{$i}", $depth, $currentDepth + 1);
                            }
                            $data['children'] = $children;
                        }
                        return $data;
                    }

                    $nestedArray = [];
                    for ($i = 0; $i < $options['random']; $i++) {
                        $prefix = "taxon_{$i}";
                        $nestedArray[] = generateNestedArray($prefix, $depth);
                    }

                    return array_merge($custom, $nestedArray);
                })
        ;
    }


    public function getName(): string
    {
        return 'taxon';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('slug')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->variableNode('translations')->cannotBeEmpty()->defaultValue([])->end()
                ->variableNode('children')->cannotBeEmpty()->defaultValue([])->end()
        ;
    }
}
