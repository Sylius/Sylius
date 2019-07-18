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

final class DressProductFixture extends AbstractFixture
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
        return 'dress_product';
    }

    public function load(array $options): void
    {
        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'dresses',
                    'translations' => [
                        'en_US' => [
                            'name' => 'Dresses',
                        ],
                        'fr_FR' => [
                            'name' => 'Robes',
                        ],
                    ],
                ],
            ],
        ]]]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Dress brand', 'code' => 'dress_brand', 'type' => TextAttributeType::TYPE],
            ['name' => 'Dress collection', 'code' => 'dress_collection', 'type' => TextAttributeType::TYPE],
            ['name' => 'Dress material', 'code' => 'dress_material', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'Dress size',
                'code' => 'dress_size',
                'values' => [
                    'dress_size_s' => 'S',
                    'dress_size_m' => 'M',
                    'dress_size_l' => 'L',
                    'dress_size_xl' => 'XL',
                    'dress_size_xxl' => 'XXL',
                ],
            ],
            [
                'name' => 'Dress height',
                'code' => 'dress_height',
                'values' => [
                    'dress_height_petite' => 'Petite',
                    'dress_height_regular' => 'Regular',
                    'dress_height_tall' => 'Tall',
                ],
            ],
        ]]);

        $products = [];

        $productsData = $this->getProductsData();

        foreach ($productsData as $productData) {

            $products[] = [
                'name' =>  $productData['name'],
                'code' => $this->faker->uuid,
                'main_taxon' => 'dress',
                'product_attributes' => [
                    'dress_brand' => $productData['brand'],
                    'dress_collection' => $productData['collection'],
                    'dress_material' => $productData['material'],
                ],
                'product_options' => ['dress_size', 'dress_height'],
                'images' => [
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, $productData['photo']),
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
                'name' => 'Circle-skirt Dress',
                'brand' => 'Wear & banana',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'dresses/dress_01.jpg'
            ],
            [
                'name' => 'Sleeveless Dress',
                'brand' => 'You are breathtaking',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'dresses/dress_02.jpg'
            ],
            [
                'name' => 'Summer tunic',
                'brand' => 'Modern Wear',
                'collection' => 'Sylius Summer 2019',
                'material' => '95% polyester, 5% elastane',
                'photo' =>'dresses/dress_03.jpg'
            ],
        ];
    }
}
