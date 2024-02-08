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

namespace Sylius\Tests\Fixture;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\Suite;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ProductAttributeFixturesTest extends KernelTestCase
{
    /** @test */
    public function fixtures_are_loaded_properly(): void
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer()->get('test.service_container', ContainerInterface::NULL_ON_INVALID_REFERENCE) ?? $kernel->getContainer();

        $fixtureRegistry = $container->get(FixtureRegistryInterface::class);
        $listenerRegistry = $container->get(ListenerRegistryInterface::class);
        $suiteLoader = $container->get(SuiteLoaderInterface::class);

        $suite = new Suite('test');
        $suite->addListener($listenerRegistry->getListener('orm_purger'), ['mode' => 'delete', 'exclude' => [], 'managers' => [null]]);
        $suite->addFixture($fixtureRegistry->getFixture('locale'), ['locales' => [], 'load_default_locale' => true]);
        $suite->addFixture($fixtureRegistry->getFixture('taxon'), ['custom' => ['books' => ['name' => 'Books', 'code' => 'BOOKS']]]);
        $suite->addFixture($fixtureRegistry->getFixture('product_attribute'), ['custom' => [
            'book_author' => ['name' => 'Author', 'code' => 'AUTHOR', 'type' => 'text'],
            'book_date' => ['name' => 'Date', 'code' => 'DATE', 'type' => 'date'],
            'book_adults_only' => ['name' => 'Adults only', 'code' => 'ADULT', 'type' => 'checkbox'],
            'book_pages' => ['name' => 'Pages', 'code' => 'PAGES', 'type' => 'integer'],
            'book_cover' => [
                'name' => 'Cover',
                'code' => 'COVER',
                'type' => 'select',
                'configuration' => [
                    'choices' => [
                        'SOFT' => ['en_US' => 'Soft'],
                        'HARD' => ['en_US' => 'Hard'],
                    ],
                ],
            ],
        ]]);
        $suite->addFixture($fixtureRegistry->getFixture('product'), ['custom' => [
            'lotr_fellowship' => [
                'name' => 'The Fellowship of the Ring',
                'code' => 'LOTR',
                'product_attributes' => [
                    'AUTHOR' => 'J.R.R Tolkien',
                    'DATE' => '19-07-1954',
                    'ADULT' => false,
                    'PAGES' => 448,
                    'COVER' => ['SOFT'],
                ],
            ],
        ]]);

        $suiteLoader->load($suite);

        $productRepository = $container->get('sylius.repository.product');

        /** @var ProductInterface $product */
        $product = $productRepository->findOneByCode('LOTR');
        $this->assertNotNull($product);

        $this->assertValueOfAttributeWithCode($product, 'DATE', new \DateTime('19-07-1954'));
        $this->assertValueOfAttributeWithCode($product, 'ADULT', false);
        $this->assertValueOfAttributeWithCode($product, 'PAGES', 448);
        $this->assertValueOfAttributeWithCode($product, 'COVER', ['SOFT']);
    }

    private function assertValueOfAttributeWithCode(ProductInterface $product, string $code, $value): void
    {
        $productAttribute = $product->getAttributeByCodeAndLocale($code, 'en_US');
        $this->assertEquals($value, $productAttribute->getValue());
    }
}
