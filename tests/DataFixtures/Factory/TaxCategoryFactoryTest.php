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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactory;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TaxCategoryFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_tax_categories(): void
    {
        $taxCategory = TaxCategoryFactory::new()->create();

        $this->assertInstanceOf(TaxCategoryInterface::class, $taxCategory->object());
        $this->assertNotNull($taxCategory->getCode());
        $this->assertNotNull($taxCategory->getName());
        $this->assertNotNull($taxCategory->getDescription());
    }

    /** @test */
    function it_creates_locales_with_custom_codes(): void
    {
        $taxCategory = TaxCategoryFactory::new()->withCode('TC1')->create();

        $this->assertEquals('TC1', $taxCategory->getCode());
    }

    /** @test */
    function it_creates_locales_with_custom_names(): void
    {
        $taxCategory = TaxCategoryFactory::new()->withName('Taxable goods')->create();

        $this->assertEquals('Taxable goods', $taxCategory->getName());
        $this->assertEquals('Taxable_goods', $taxCategory->getCode());
    }

    /** @test */
    function it_creates_locales_with_custom_descriptions(): void
    {
        $taxCategory = TaxCategoryFactory::new()->withDescription('Taxable goods are evil.')->create();

        $this->assertEquals('Taxable goods are evil.', $taxCategory->getDescription());
    }
}
