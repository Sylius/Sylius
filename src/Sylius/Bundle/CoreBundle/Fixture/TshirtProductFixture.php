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

use Faker\Factory;
use Faker\Generator;

trigger_deprecation(
    'sylius/core-bundle',
    '1.5',
    'The "TshirtProductFixture" class is deprecated. Use new product fixtures class located at "src/Sylius/Bundle/CoreBundle/Fixture/" instead.',
);

use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated since Sylius 1.5 and will be removed in Sylius 2.0. Use new product fixtures class located at "src/Sylius/Bundle/CoreBundle/Fixture/" instead.
 */
class TshirtProductFixture extends AbstractFixture
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private AbstractResourceFixture $taxonFixture,
        private AbstractResourceFixture $productAttributeFixture,
        private AbstractResourceFixture $productOptionFixture,
        private AbstractResourceFixture $productFixture,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('amount')
                ->setAllowedTypes('amount', 'int')
        ;
    }

    public function getName(): string
    {
        return 'tshirt_product';
    }

    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 't_shirts',
                    'name' => 'T-Shirts',
                    'slug' => 't-shirts',
                    'children' => [
                        [
                            'code' => 'mens_t_shirts',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Men',
                                    'slug' => 't-shirts/men',
                                ],
                                'fr_FR' => [
                                    'name' => 'Hommes',
                                    'slug' => 't-shirts/hommes',
                                ],
                            ],
                        ],
                        [
                            'code' => 'womens_t_shirts',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Women',
                                    'slug' => 't-shirts/women',
                                ],
                                'fr_FR' => [
                                    'name' => 'Hommes',
                                    'slug' => 't-shirts/femmes',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]]]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'T-Shirt brand', 'code' => 't_shirt_brand', 'type' => TextAttributeType::TYPE],
            ['name' => 'T-Shirt collection', 'code' => 't_shirt_collection', 'type' => TextAttributeType::TYPE],
            ['name' => 'T-Shirt material', 'code' => 't_shirt_material', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'T-Shirt color',
                'code' => 't_shirt_color',
                'values' => [
                    't_shirt_color_red' => 'Red',
                    't_shirt_color_black' => 'Black',
                    't_shirt_color_white' => 'White',
                ],
            ],
            [
                'name' => 'T-Shirt size',
                'code' => 't_shirt_size',
                'values' => [
                    't_shirt_size_s' => 'S',
                    't_shirt_size_m' => 'M',
                    't_shirt_size_l' => 'L',
                    't_shirt_size_xl' => 'XL',
                    't_shirt_size_xxl' => 'XXL',
                ],
            ],
        ]]);

        $products = [];
        $productsNames = $this->getUniqueNames($options['amount']);
        for ($i = 0; $i < $options['amount']; ++$i) {
            $categoryTaxonCode = $this->faker->randomElement(['mens_t_shirts', 'womens_t_shirts']);

            $products[] = [
                'name' => sprintf('T-Shirt "%s"', $productsNames[$i]),
                'code' => $this->faker->uuid,
                'main_taxon' => $categoryTaxonCode,
                'taxons' => ['t_shirts', $categoryTaxonCode],
                'product_attributes' => [
                    't_shirt_brand' => $this->faker->randomElement(['Nike', 'Adidas', 'JKM-476 Streetwear', 'Potato', 'Centipede Wear']),
                    't_shirt_collection' => sprintf('Sylius %s %s', $this->faker->randomElement(['Summer', 'Winter', 'Spring', 'Autumn']), random_int(1995, 2012)),
                    't_shirt_material' => $this->faker->randomElement(['Centipede', 'Wool', 'Centipede 10% / Wool 90%', 'Potato 100%']),
                ],
                'product_options' => ['t_shirt_color', 't_shirt_size'],
                'images' => [
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 't-shirts.jpg'),
                        'type' => 'main',
                    ],
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 't-shirts.jpg'),
                        'type' => 'thumbnail',
                    ],
                ],
            ];
        }

        $this->productFixture->load(['custom' => $products]);
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }

    private function getUniqueNames(int $amount): array
    {
        $productsNames = [];

        for ($i = 0; $i < $amount; ++$i) {
            $name = $this->faker->word;
            while (in_array($name, $productsNames)) {
                $name = $this->faker->word;
            }
            $productsNames[] = $name;
        }

        return $productsNames;
    }
}
