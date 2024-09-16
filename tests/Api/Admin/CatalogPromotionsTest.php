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

namespace Sylius\Tests\Api\Admin;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CatalogPromotionsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();

        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultPutHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_catalog_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'catalog_promotion/catalog_promotion.yaml']);

        $this->requestGet('/api/v2/admin/catalog-promotions');

        $this->assertResponseSuccessful('admin/catalog_promotion/get_catalog_promotions_response');
    }

    /** @test */
    public function it_gets_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();

        $this->requestGet(sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()));

        $this->assertResponseSuccessful('admin/catalog_promotion/get_catalog_promotion_response');
    }

    /** @test */
    public function it_creates_a_catalog_promotion(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/catalog-promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'startDate' => '2022-01-01',
                'endDate' => '2022-01-02',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'actions' => [
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'amount' => 0.5,
                        ],
                    ],
                ],
                'scopes' => [
                    [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'variants' => [
                                'MUG',
                            ],
                        ],
                    ],
                ],
                'translations' => ['en_US' => [
                    'label' => 'T-Shirts discount',
                    'description' => '50% discount on every T-Shirt',
                ]],
                'enabled' => true,
                'exclusive' => false,
                'priority' => 100,
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/catalog_promotion/post_catalog_promotion_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_without_required_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $this->requestPost(uri: '/api/v2/admin/catalog-promotions', body: []);

        $this->assertResponseUnprocessableEntity('admin/catalog_promotion/post_catalog_promotion_without_required_data_response');
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_with_taken_code(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'catalog_promotion/catalog_promotion.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/catalog-promotions',
            body: [
                'name' => 'Mugs discount',
                'code' => 'mugs_discount',
            ],
        );

        $this->assertResponseUnprocessableEntity(
            'admin/catalog_promotion/post_catalog_promotion_with_taken_code_response',
        );
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_with_end_date_earlier_than_start_date(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'catalog_promotion/catalog_promotion.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/catalog-promotions',
            body: [
                'name' => 'calatog Promotion',
                'code' => 'catalog_promotion',
                'startDate' => '2021-11-04 10:42:00',
                'endDate' => '2021-10-04 10:42:00',
            ],
        );

        $this->assertResponseUnprocessableEntity(
            'admin/catalog_promotion/post_catalog_promotion_with_invalid_dates_response',
        );
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_with_invalid_scopes(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
            'taxon_image.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/catalog-promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'actions' => [
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'amount' => 0.5,
                        ],
                    ],
                ],
                'scopes' => [
                    [
                        'type' => 'invalid_type',
                        'configuration' => [
                            'variants' => ['MUG'],
                        ],
                    ], [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [],
                    ], [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'variants' => [],
                        ],
                    ], [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'variants' => [
                                'MUG',
                                'MUG',
                            ],
                        ],
                    ], [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'variants' => [
                                '',
                                'invalid_variant',
                            ],
                        ],
                    ], [
                        'type' => InForProductScopeVariantChecker::TYPE,
                        'configuration' => [],
                    ], [
                        'type' => InForProductScopeVariantChecker::TYPE,
                        'configuration' => [
                            'products' => [],
                        ],
                    ],  [
                        'type' => InForProductScopeVariantChecker::TYPE,
                        'configuration' => [
                            'products' => [
                                'MUG_SW',
                                'MUG_SW',
                            ],
                        ],
                    ], [
                        'type' => InForProductScopeVariantChecker::TYPE,
                        'configuration' => [
                            'products' => [
                                '',
                                'invalid_product',
                            ],
                        ],
                    ], [
                        'type' => InForTaxonsScopeVariantChecker::TYPE,
                        'configuration' => [],
                    ], [
                        'type' => InForTaxonsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'taxons' => [],
                        ],
                    ],  [
                        'type' => InForTaxonsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'taxons' => [
                                'CATEGORY',
                                'CATEGORY',
                            ],
                        ],
                    ], [
                        'type' => InForTaxonsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'taxons' => [
                                '',
                                'invalid_taxon',
                            ],
                        ],
                    ],
                ],
                'translations' => ['en_US' => [
                    'label' => 'T-Shirts discount',
                    'description' => '50% discount on every T-Shirt',
                ]],
                'enabled' => true,
                'exclusive' => false,
            ],
        );

        $this->assertJsonResponseViolations($this->client->getResponse(), [
            [
                'propertyPath' => 'scopes[0].type',
                'message' => 'Catalog promotion scope type is invalid. Available types are for_products, for_taxons, for_variants.',
            ],
            [
                'propertyPath' => 'scopes[1].configuration[variants]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'scopes[2].configuration[variants]',
                'message' => 'Please add at least 1 variant.',
            ],
            [
                'propertyPath' => 'scopes[3].configuration[variants]',
                'message' => 'Provided configuration contains errors. Please add only unique variants.',
            ],
            [
                'propertyPath' => 'scopes[4].configuration[variants][0]',
                'message' => 'This value should not be blank.',
            ],
            [
                'propertyPath' => 'scopes[4].configuration[variants][1]',
                'message' => 'Product variant with code invalid_variant does not exist.',
            ],
            [
                'propertyPath' => 'scopes[5].configuration[products]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'scopes[6].configuration[products]',
                'message' => 'Provided configuration contains errors. Please add at least 1 product.',
            ],
            [
                'propertyPath' => 'scopes[7].configuration[products]',
                'message' => 'Provided configuration contains errors. Please add only unique products.',
            ],
            [
                'propertyPath' => 'scopes[8].configuration[products][0]',
                'message' => 'This value should not be blank.',
            ],
            [
                'propertyPath' => 'scopes[8].configuration[products][1]',
                'message' => 'Product with code invalid_product does not exist.',
            ],
            [
                'propertyPath' => 'scopes[9].configuration[taxons]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'scopes[10].configuration[taxons]',
                'message' => 'Provided configuration contains errors. Please add at least 1 taxon.',
            ],
            [
                'propertyPath' => 'scopes[11].configuration[taxons]',
                'message' => 'Provided configuration contains errors. Please add only unique taxons.',
            ],
            [
                'propertyPath' => 'scopes[12].configuration[taxons][0]',
                'message' => 'This value should not be blank.',
            ],
            [
                'propertyPath' => 'scopes[12].configuration[taxons][1]',
                'message' => 'Taxon with code invalid_taxon does not exist.',
            ],
        ]);
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_with_invalid_actions(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/catalog-promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'actions' => [
                    [
                        'type' => '',
                        'configuration' => [
                            'amount' => 0.5,
                        ],
                    ],
                    [
                        'type' => 'invalid_type',
                        'configuration' => [
                            'amount' => 0.5,
                        ],
                    ],
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [],
                    ],
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'amount' => null,
                        ],
                    ],
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'amount' => 1.5,
                        ],
                    ],
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'amount' => 'text',
                        ],
                    ],
                    [
                        'type' => FixedDiscountPriceCalculator::TYPE,
                        'configuration' => [],
                    ],
                    [
                        'type' => FixedDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'invalid_channel' => [
                                'amount' => 1000,
                            ],
                        ],
                    ],
                    [
                        'type' => FixedDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'WEB' => [],
                        ],
                    ],
                    [
                        'type' => FixedDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => null,
                            ],
                        ],
                    ],
                    [
                        'type' => FixedDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 'text',
                            ],
                        ],
                    ],
                ],
                'scopes' => [
                    [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'variants' => [
                                'MUG',
                            ],
                        ],
                    ],
                ],
                'translations' => ['en_US' => [
                    'label' => 'T-Shirts discount',
                    'description' => '50% discount on every T-Shirt',
                ]],
                'enabled' => true,
                'exclusive' => false,
            ],
        );

        $this->assertJsonResponseViolations($this->client->getResponse(), [
            [
                'propertyPath' => 'actions[0].type',
                'message' => 'Please choose an action type.',
            ],
            [
                'propertyPath' => 'actions[1].type',
                'message' => 'Catalog promotion action type is invalid. Available types are fixed_discount, percentage_discount.',
            ],
            [
                'propertyPath' => 'actions[2].configuration[amount]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'actions[3].configuration[amount]',
                'message' => 'The percentage discount amount must be a number and can not be empty.',
            ],
            [
                'propertyPath' => 'actions[4].configuration[amount]',
                'message' => 'The percentage discount amount must be between 0% and 100%.',
            ],
            [
                'propertyPath' => 'actions[5].configuration[amount]',
                'message' => 'The percentage discount amount must be a number and can not be empty.',
            ],
            [
                'propertyPath' => 'actions[5].configuration[amount]',
                'message' => 'This value should be a valid number.',
            ],
            [
                'propertyPath' => 'actions[6].configuration[WEB]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'actions[7].configuration[WEB]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'actions[8].configuration[WEB][amount]',
                'message' => 'This field is missing.',
            ],
            [
                'propertyPath' => 'actions[9].configuration[WEB][amount]',
                'message' => 'Provided configuration contains errors. Please add the fixed discount amount that is a number greater than 0.',
            ],
            [
                'propertyPath' => 'actions[10].configuration[WEB][amount]',
                'message' => 'Provided configuration contains errors. Please add the fixed discount amount that is a number greater than 0.',
            ],
        ]);
    }

    /** @test */
    public function it_updates_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();

        $this->requestPut(
            uri: sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()),
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'new_code',
                'actions' => [
                    [
                        'type' => PercentageDiscountPriceCalculator::TYPE,
                        'configuration' => [
                            'amount' => 0.4,
                        ],
                    ],
                ],
                'scopes' => [
                    [
                        'type' => InForVariantsScopeVariantChecker::TYPE,
                        'configuration' => [
                            'variants' => [
                                'MUG',
                            ],
                        ],
                    ],
                ],
                'channels' => [
                    '/api/v2/admin/channels/MOBILE',
                ],
                'translations' => ['en_US' => [
                    '@id' => sprintf(
                        '/api/v2/admin/catalog-promotions/%s/translations/%s',
                        $catalogPromotion->getCode(),
                        $catalogPromotion->getTranslation('en_US')->getLocale(),
                    ),
                    'label' => 'T-Shirts discount: edited',
                ]],
                'enabled' => true,
                'exclusive' => false,
                'priority' => 1000,
            ],
        );

        $this->assertResponseSuccessful('admin/catalog_promotion/put_catalog_promotion_response');
    }

    /** @test */
    public function it_does_not_update_a_catalog_promotion_with_duplicate_locale_translation(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();

        $this->requestPut(
            uri: sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()),
            body: [
                'translations' => [
                    'en_US' => [
                        'slug' => 'caps/cap',
                        'name' => 'Cap',
                    ],
                ],
            ],
        );

        $this->assertResponseUnprocessableEntity(
            'admin/catalog_promotion/put_catalog_promotion_with_duplicate_locale_translation',
        );
    }

    /** @test */
    public function it_deletes_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();

        $this->requestDelete(sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
    }

    private function loadFixturesAndGetCatalogPromotion(): CatalogPromotionInterface
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
            'catalog_promotion/catalog_promotion.yaml',
        ]);

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $fixtures['catalog_promotion'];

        return $catalogPromotion;
    }
}
