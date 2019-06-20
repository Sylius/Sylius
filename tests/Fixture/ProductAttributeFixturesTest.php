<?php

declare(strict_types=1);

namespace Sylius\Tests\Fixture;

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

        $fixtureRegistry = $container->get('sylius_fixtures.fixture_registry');
        $listenerRegistry = $container->get('sylius_fixtures.listener_registry');
        $suiteLoader = $container->get('sylius_fixtures.suite_loader');

        $suite = new Suite('test');
        $suite->addListener($listenerRegistry->getListener('orm_purger'), ['mode' => 'delete', 'exclude' => [], 'managers' => [null]]);
        $suite->addFixture($fixtureRegistry->getFixture('locale'), ['locales' => ['en_US'], 'load_default_locale' => false]);
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
