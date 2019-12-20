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

namespace Sylius\Tests\Fixture;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\Suite;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class ProductFixturesTest extends KernelTestCase
{
    /**
     * @var KernelInterface
     */
    private $testKernel;

    /**
     * @var ContainerInterface
     */
    private $testContainer;

    /**
     * @var FixtureRegistryInterface
     */
    private $fixtureRegistry;

    /**
     * @var ListenerRegistryInterface
     */
    private $listenerRegistry;

    /**
     * @var SuiteLoaderInterface
     */
    private $suiteLoader;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function setUp(): void
    {
        $this->testKernel = static::bootKernel();
        $this->testContainer = $this->testKernel->getContainer()->get('test.service_testContainer', ContainerInterface::NULL_ON_INVALID_REFERENCE) ?? $this->testKernel->getContainer();

        $this->fixtureRegistry = $this->testContainer->get(FixtureRegistryInterface::class);
        $this->listenerRegistry = $this->testContainer->get(ListenerRegistryInterface::class);
        $this->suiteLoader = $this->testContainer->get(SuiteLoaderInterface::class);
        $this->productRepository = $this->testContainer->get('sylius.repository.product');

        parent::setUp();
    }

    /** @test */
    public function fixtures_are_loaded_properly(): void
    {
        $suite = new Suite('test');
        $suite->addListener($this->listenerRegistry->getListener('orm_purger'), ['mode' => 'delete', 'exclude' => [], 'managers' => [null]]);
        $suite->addFixture($this->fixtureRegistry->getFixture('locale'), ['locales' => [], 'load_default_locale' => true]);
        $suite->addFixture($this->fixtureRegistry->getFixture('taxon'), ['custom' => ['books' => ['name' => 'Books', 'code' => 'BOOKS']]]);
        $suite->addFixture($this->fixtureRegistry->getFixture('product'), $this->getFixtureTestData());
        $this->suiteLoader->load($suite);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode('LOTR');
        $this->assertNotNull($product);

        $this->assertEquals('LOTR', $product->getCode());
        $this->assertEquals('The Fellowship of the Ring', $product->getName());
    }

    /** @test */
    public function fixtures_can_use_translated_products(): void
    {
        $suite = new Suite('test');
        $suite->addListener($this->listenerRegistry->getListener('orm_purger'), ['mode' => 'delete', 'exclude' => [], 'managers' => [null]]);
        $suite->addFixture($this->fixtureRegistry->getFixture('locale'), ['locales' => [], 'load_default_locale' => true]);
        $suite->addFixture($this->fixtureRegistry->getFixture('taxon'), ['custom' => ['books' => ['name' => 'Books', 'code' => 'BOOKS']]]);
        $suite->addFixture($this->fixtureRegistry->getFixture('product'), $this->getFixtureTestDataWithTranslations());
        $this->suiteLoader->load($suite);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode('LOTR');

        $this->assertEquals('LOTR', $product->getCode());
        $this->assertEquals('The Fellowship of the Ring', $product->getName());

        $product->setCurrentLocale('nl_NL');

        $this->assertEquals('LOTR', $product->getCode());
        $this->assertEquals('In de ban van de ring', $product->getName());
    }

    /** @test */
    public function fixtures_can_run_with_incomplete_translations(): void
    {
        $suite = new Suite('test');
        $suite->addListener($this->listenerRegistry->getListener('orm_purger'), ['mode' => 'delete', 'exclude' => [], 'managers' => [null]]);
        $suite->addFixture($this->fixtureRegistry->getFixture('locale'), ['locales' => [], 'load_default_locale' => true]);
        $suite->addFixture($this->fixtureRegistry->getFixture('taxon'), ['custom' => ['books' => ['name' => 'Books', 'code' => 'BOOKS']]]);
        $suite->addFixture($this->fixtureRegistry->getFixture('product'), $this->getFixtureTestDataWithIncompleteTranslations());
        $this->suiteLoader->load($suite);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode('LOTR');

        $this->assertEquals('LOTR', $product->getCode());
        $this->assertEquals('The Fellowship of the Ring', $product->getName());

        $product->setCurrentLocale('nl_NL');

        $this->assertEquals('LOTR', $product->getCode());
        $this->assertEquals('In de ban van de ring', $product->getName());
    }

    private function getFixtureTestData(): array
    {
        return [
            'custom' => [
                'lotr_fellowship' => [
                    'name' => 'The Fellowship of the Ring',
                    'code' => 'LOTR',
                    'short_description' => 'An epic high-fantasy novel written by English author and scholar J. R. R. Tolkien.',
                    'description' => 'The title of the novel refers to the story\'s main antagonist, the Dark Lord Sauron, who had in an earlier age created the One Ring to rule the other Rings of Power as the ultimate weapon in his campaign to conquer and rule all of Middle-earth',
                    'main_taxon' => 'BOOKS',
                ],
            ]
        ];
    }

    private function getFixtureTestDataWithTranslations(): array
    {
        $original = $this->getFixtureTestData();

        $translations = [
            'nl_NL' => [
                'name' => 'In de ban van de ring',
                'short_description' => 'Een fantasy-werk geschreven door de taalkundige en universitair professor J.R.R. Tolkien.',
                'description' => 'De Engelstalige titel van het werk verwijst naar de belangrijkste antagonist in het verhaal: de Zwarte Heerser Sauron, die ooit de Ene Ring creëerde om op die manier de andere Ringen van Macht te regeren.',
            ]
        ];

        $original['custom']['lotr_fellowship']['translations'] = $translations;

        return $original;
    }

    private function getFixtureTestDataWithIncompleteTranslations(): array
    {
        $original = $this->getFixtureTestData();

        $translations = [
            'nl_NL' => [
                'name' => 'In de ban van de ring',
            ]
        ];

        $original['custom']['lotr_fellowship']['translations'] = $translations;

        return $original;
    }
}
