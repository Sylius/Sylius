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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductOptionFactory;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ProductOptionFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_product_options(): void
    {
        $productOption = ProductOptionFactory::createOne();

        $this->assertInstanceOf(ProductOptionInterface::class, $productOption->object());
        $this->assertNotNull($productOption->getCode());
        $this->assertCount(5, $productOption->getValues());
    }

    /** @test */
    function it_creates_product_options_with_custom_codes(): void
    {
        $productOption = ProductOptionFactory::new()->withCode('color')->create();

        $this->assertEquals('color', $productOption->getCode());
    }

    /** @test */
    function it_creates_product_options_with_translations_for_each_locales(): void
    {
        LocaleFactory::createMany(2);

        $productOption = ProductOptionFactory::createOne();

        $this->assertCount(2, $productOption->getTranslations());
    }

    /** @test */
    function it_creates_product_options_with_custom_names(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $productOption = ProductOptionFactory::new()->withName('Color')->create();

        // test en_US translation
        $productOption->setCurrentLocale('en_US');
        $productOption->setFallbackLocale('en_US');
        $this->assertEquals('Color', $productOption->getName());
        $this->assertEquals('Color', $productOption->getCode());

        // test fr_FR translation
        $productOption->setCurrentLocale('fr_FR');
        $productOption->setFallbackLocale('en_Ufr_FRS');
        $this->assertEquals('Color', $productOption->getName());
        $this->assertEquals('Color', $productOption->getCode());
    }

    /** @test */
    function it_creates_product_options_with_custom_values(): void
    {
        LocaleFactory::createOne();

        $productOption = ProductOptionFactory::new()->withValues([
            'blue' => 'Blue',
            'green' => 'Green',
            'red' => 'Red',
        ])->create();

        $this->assertCount(3, $productOption->getValues());

        /** @var ProductOptionValueInterface $blue */
        $blue = $productOption->getValues()->first();
        $this->assertEquals('blue', $blue->getCode());
        $this->assertEquals('Blue', $blue->getValue());
    }
}
