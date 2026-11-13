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

namespace Fixture;

use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\Suite;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ProductOptionFixturesTest extends KernelTestCase
{
    #[Test]
    public function fixtures_are_loaded_properly(): void
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer()->get('test.service_container', ContainerInterface::NULL_ON_INVALID_REFERENCE) ?? $kernel->getContainer();

        $fixtureRegistry = $container->get(FixtureRegistryInterface::class);
        $listenerRegistry = $container->get(ListenerRegistryInterface::class);
        /** @var SuiteLoaderInterface $suiteLoader */
        $suiteLoader = $container->get(SuiteLoaderInterface::class);

        $suite = new Suite('test');
        $suite->addListener($listenerRegistry->getListener('orm_purger'), ['mode' => 'delete', 'exclude' => [], 'managers' => [null]]);
        $suite->addFixture($fixtureRegistry->getFixture('locale'), ['locales' => [], 'load_default_locale' => true]);
        $suite->addFixture($fixtureRegistry->getFixture('taxon'), ['custom' => ['books' => ['name' => 'Books', 'code' => 'BOOKS']]]);
        $suite->addFixture($fixtureRegistry->getFixture('product_option'), ['custom' => [
            'dress_size' => [
                'name' => 'Dress height',
                'code' => 'dress_height',
                'translations' => [
                    'en_US' => [
                        'values' => [
                            'dress_height_petite' => 'Petite',
                            'dress_height_regular' => 'Regular',
                            'dress_height_tall' => 'Tall',
                        ],
                    ],
                    'fr_FR' => [
                        'name' => 'Taille de robe',
                        'values' => [
                            'dress_height_petite' => 'Petite',
                            'dress_height_regular' => 'Moyenne',
                            'dress_height_tall' => 'Grande',
                        ],
                    ],
                ],
            ],
        ]]);
        $suite->addFixture($fixtureRegistry->getFixture('product'), ['custom' => [
            'sunshine_strappy_delight' => [
                'name' => 'Sunshine Strappy Delight',
                'code' => 'sunshine_strappy_delight',
                'product_options' => [
                    'dress_height',
                ],
            ],
        ]]);

        $suiteLoader->load($suite);

        $productRepository = $container->get('sylius.repository.product');

        /** @var ProductInterface $product */
        $product = $productRepository->findOneByCode('sunshine_strappy_delight');
        $this->assertNotNull($product);

        $productOptionRepository = $container->get('sylius.repository.product_option');

        /** @var ProductOptionInterface $productOption */
        $productOption = $productOptionRepository->findOneByCode('dress_height');

        $productOption->setCurrentLocale('en_US');
        $this->assertSame('Dress height', $productOption->getName());

        $productOption->setCurrentLocale('fr_FR');
        $this->assertSame('Taille de robe', $productOption->getName());

        $this->assertValuesOfOptionWithCode($product, 'dress_height', 'en_US', [
            'dress_height_petite' => 'Petite',
            'dress_height_regular' => 'Regular',
            'dress_height_tall' => 'Tall',
        ]);

        $this->assertValuesOfOptionWithCode($product, 'dress_height', 'fr_FR', [
            'dress_height_petite' => 'Petite',
            'dress_height_regular' => 'Moyenne',
            'dress_height_tall' => 'Grande',
        ]);
    }

    private function assertValuesOfOptionWithCode(ProductInterface $product, string $code, string $locale, array $expectedValues): void
    {
        foreach ($product->getOptions() as $productOption) {
            if ($code !== $productOption->getCode()) {
                continue;
            }

            $values = [];
            foreach ($productOption->getValues() as $productOptionValue) {
                $productOptionValue->setCurrentLocale($locale);
                $values[$productOptionValue->getCode()] = $productOptionValue->getValue();
            }

            $this->assertSame($expectedValues, $values);
        }
    }
}
