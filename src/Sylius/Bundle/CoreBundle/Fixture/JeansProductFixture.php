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

use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;

final class JeansProductFixture extends AbstractFixture
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
    }

    public function getName(): string
    {
        return 'jeans_product';
    }

    public function load(array $options): void
    {
        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'jeans',
                    'name' => 'Jeans',
                    'slug' => 'jeans',
                    'children' => [
                        [
                            'code' => 'mens_jeans',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Men',
                                    'slug' => 'jeans/men',
                                ],
                                'fr_FR' => [
                                    'name' => 'Hommes',
                                    'slug' => 'jeans/hommes',
                                ],
                            ],
                        ],
                        [
                            'code' => 'womens_jeans',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Women',
                                    'slug' => 'jeans/women',
                                ],
                                'fr_FR' => [
                                    'name' => 'Femme',
                                    'slug' => 'jeans/femmes',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]]]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Jeans brand', 'code' => 'jeans_brand', 'type' => TextAttributeType::TYPE],
            ['name' => 'Jeans collection', 'code' => 'jeans_collection', 'type' => TextAttributeType::TYPE],
            ['name' => 'Jeans material', 'code' => 'jeans_material', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'Jeans size',
                'code' => 'jeans_size',
                'values' => [
                    'jeans_size_s' => 'S',
                    'jeans_size_m' => 'M',
                    'jeans_size_l' => 'L',
                    'jeans_size_xl' => 'XL',
                    'jeans_size_xxl' => 'XXL',
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
                'taxons' => ['jeans', $categoryTaxonCode],
                'product_attributes' => [
                    'jeans_brand' => $productData['brand'],
                    'jeans_collection' => $productData['collection'],
                    'jeans_material' => $productData['material'],
                ],
                'product_options' => ['jeans_size'],
                'images' => [
                    [
                        'path' => sprintf('%s/../Resources/fixtures/jeans/%s', __DIR__, $productData['photo']),
                        'type' => 'main',
                    ],
                ],
            ];
        }

        $this->productFixture->load(['custom' => $products]);
    }

    private function getProductsData(): array
    {
        return $products = [
            [
                'name' => 'Basic regular',
                'categoryTaxonCode' => 'mens_jeans',
                'brand' => 'You are breathtaking',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% viscose',
                'photo' =>'man/jeans_01.jpg'
            ],
            [
                'name' => 'Slim fit classic',
                'categoryTaxonCode' => 'mens_jeans',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% cotton',
                'photo' =>'man/jeans_02.jpg'
            ],
            [
                'name' => 'Regular Fit casual',
                'categoryTaxonCode' => 'mens_jeans',
                'brand' => 'Celsius Small',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% viscose',
                'photo' =>'man/jeans_03.jpg'
            ],
            [
                'name' => 'Slim fit elegant',
                'categoryTaxonCode' => 'mens_jeans',
                'brand' => 'Date & Banana',
                'collection' => 'Sylius Winter 2019',
                'material' => '51% viscose, 29% polyester, 20% nylon',
                'photo' =>'man/jeans_04.jpg'
            ],
            [
                'name' => 'Ultra slim ',
                'categoryTaxonCode' => 'womens_jeans',
                'brand' => 'You are breathtaking',
                'collection' => 'Sylius Winter 2019',
                'material' => '100% linen',
                'photo' =>'woman/jeans_01.jpg'
            ],
            [
                'name' => 'Slim fit ',
                'categoryTaxonCode' => 'womens_jeans',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'woman/jeans_02.jpg'
            ],
            [
                'name' => 'New age regular',
                'categoryTaxonCode' => 'womens_jeans',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'woman/jeans_03.jpg'
            ],
            [
                'name' => 'Whole holes classic',
                'categoryTaxonCode' => 'womens_jeans',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'woman/jeans_04.jpg'
            ],
        ];
    }
}
