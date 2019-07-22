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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CapsProductFixture extends AbstractFixture
{
    /** @var AbstractResourceFixture */
    private $taxonFixture;

    /** @var AbstractResourceFixture */
    private $productAttributeFixture;

    /** @var AbstractResourceFixture */
    private $productOptionFixture;

    /** @var AbstractResourceFixture */
    private $productFixture;

    /** @var string */
    private $baseLocaleCode;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        AbstractResourceFixture $taxonFixture,
        AbstractResourceFixture $productAttributeFixture,
        AbstractResourceFixture $productOptionFixture,
        AbstractResourceFixture $productFixture,
        string $baseLocaleCode
    ) {
        $this->taxonFixture = $taxonFixture;
        $this->productAttributeFixture = $productAttributeFixture;
        $this->productOptionFixture = $productOptionFixture;
        $this->productFixture = $productFixture;
        $this->baseLocaleCode = $baseLocaleCode;

        $this->faker = \Faker\Factory::create();

        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('tax_category')
                ->setAllowedTypes('tax_category', 'string')
        ;
    }

    public function getName(): string
    {
        return 'caps_product';
    }

    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'caps',
                    'name' => 'Caps',
                    'slug' => 'caps',
                    'children' => [
                        [
                            'code' => 'simple_caps',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Simple',
                                    'slug' => 'caps/simple',
                                ],
                                'fr_FR' => [
                                    'name' => 'Simple',
                                    'slug' => 'casquette/simple',
                                ],
                            ],
                        ],
                        [
                            'code' => 'caps_with_pompons',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'With pompons',
                                    'slug' => 'caps/with-pompons',
                                ],
                                'fr_FR' => [
                                    'name' => 'A pompon',
                                    'slug' => 'casquette/a-pompon',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]]]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Cap brand', 'code' => 'cap_brand', 'type' => TextAttributeType::TYPE],
            ['name' => 'Cap collection', 'code' => 'cap_collection', 'type' => TextAttributeType::TYPE],
            ['name' => 'Cap material', 'code' => 'cap_material', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
              'name' => 'Cap color',
              'code' => 'cap-color',
              'values' => [
                  'cap_color_red' => 'Red',
                  'cap_color_black' => 'Black',
                  'cap_color_white' => 'White',
              ],
            ],
            [
                'name' => 'Cap size',
                'code' => 'cap_size',
                'values' => [
                    'caps_size_s' => 'S',
                    'caps_size_m' => 'M',
                    'caps_size_l' => 'L',
                    'caps_size_xl' => 'XL',
                    'caps_size_xxl' => 'XXL',
                ],
            ],
        ]]);

        $products = [];

        $productsData = $this->getProductsData();

        foreach ($productsData as $productData) {
            $categoryTaxonCode = $productData['categoryTaxonCode'];

            $products[] = [
                'name' =>  $productData['name'],
                'code' => $this->faker->uuid,
                'main_taxon' => $categoryTaxonCode,
                'taxons' => ['caps', $categoryTaxonCode],
                'product_attributes' => [
                    'cap_brand' => $productData['brand'],
                    'cap_collection' => $productData['collection'],
                    'cap_material' => $productData['material'],
                ],
                'product_options' => ['cap-color', 'cap_size'],
                'images' => [
                    [
                        'path' => sprintf('%s/../Resources/fixtures/caps/%s', __DIR__, $productData['photo']),
                        'type' => 'main',
                    ],
                ],
                'tax_category' => $options['tax_category'],
            ];
        }

        $this->productFixture->load(['custom' => $products]);
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
            ->scalarNode('tax_category')->cannotBeEmpty()->end();
        ;
    }

    private function getProductsData(): array
    {
        return $products = [
            [
                'name' => 'Basic winter hot cap',
                'categoryTaxonCode' => 'caps_with_pompons',
                'brand' => 'You are breathtaking',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% wool',
                'photo' =>'cap_01.jpg'
            ],
            [
                'name' => 'Beautiful cap for woman ',
                'categoryTaxonCode' => 'simple_caps',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% wool',
                'photo' =>'cap_02.jpg'
            ],
            [
                'name' => 'Regular cap with big pompon',
                'categoryTaxonCode' => 'caps_with_pompons',
                'brand' => 'Celsius Small',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% wool',
                'photo' =>'cap_03.jpg'
            ],
            [
                'name' => 'Simple cap',
                'categoryTaxonCode' => 'simple_caps',
                'brand' => 'Date & Banana',
                'collection' => 'Sylius Winter 2019',
                'material' => '51% wool, 29% polyester, 20% nylon',
                'photo' =>'cap_04.jpg'
            ],
        ];
    }
}
