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

final class NewTshirtProductFixture extends AbstractFixture
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
        return 'new_tshirt_product';
    }

    public function load(array $options): void
    {
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
                                    'name' => 'Femme',
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

        $productsData = $this->getProductsData();

        foreach ($productsData as $productData) {
            $categoryTaxonCode = $productData['categoryTaxonCode'];

            $products[] = [
                'name' =>  $productData['name'],
                'code' => $this->faker->uuid,
                'main_taxon' => $categoryTaxonCode,
                'taxons' => ['t_shirts', $categoryTaxonCode],
                'product_attributes' => [
                    't_shirt_brand' => $productData['brand'],
                    't_shirt_collection' => $productData['collection'],
                    't_shirt_material' => $productData['material'],
                ],
                'product_options' => ['t_shirt_size'],
                'images' => [
                    [
                        'path' => sprintf('%s/../Resources/fixtures/t-shirts/%s', __DIR__, $productData['photo']),
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
                'name' => 'Basic regular woman',
                'gender' => 'women',
                'categoryTaxonCode' => 'womens_t_shirts',
                'brand' => 'You are breathtaking',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% viscose',
                'photo' =>'woman/t-shirt_01.jpg'
            ],
            [
                'name' => 'Slim fit woman',
                'gender' => 'women',
                'categoryTaxonCode' => 'womens_t_shirts',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% cotton',
                'photo' =>'woman/t-shirt_02.jpg'
            ],
            [
                'name' => 'Regular Fit V-neck woman',
                'gender' => 'women',
                'categoryTaxonCode' => 'womens_t_shirts',
                'brand' => 'Celsius Small',
                'collection' => 'Sylius Summer 2019',
                'material' => '100% viscose',
                'photo' =>'woman/t-shirt_03.jpg'
            ],
            [
                'name' => 'Slim fit men',
                'gender' => 'men',
                'categoryTaxonCode' => 'mens_t_shirts',
                'brand' => 'Date & Banana',
                'collection' => 'Sylius Winter 2019',
                'material' => '51% viscose, 29% polyester, 20% nylon',
                'photo' =>'man/t-shirt_01.jpg'
            ],
            [
                'name' => 'Regular fit men',
                'gender' => 'men',
                'categoryTaxonCode' => 'mens_t_shirts',
                'brand' => 'You are breathtaking',
                'collection' => 'Sylius Winter 2019',
                'material' => '100% linen',
                'photo' =>'man/t-shirt_02.jpg'
            ],
            [
                'name' => 'Slim fit V-neck men',
                'gender' => 'men',
                'categoryTaxonCode' => 'mens_t_shirts',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'man/t-shirt_03.jpg'
            ],
        ];
    }
}
