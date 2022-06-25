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

namespace Sylius\Bundle\TaxationBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\TaxationBundle\DependencyInjection\SyliusTaxationExtension;
use Sylius\Component\Taxation\Attribute\AsTaxCalculator;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class SyliusTaxationExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_tax_calculators_with_attribute(): void
    {
        $this->container->register(
            'acme.tax_calculator_autoconfigured',
            DummyTaxCalculator::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.tax_calculator_autoconfigured',
            'sylius.tax_calculator',
            ['calculator' => 'dummyTaxCalculator']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusTaxationExtension()];
    }
}

#[AsTaxCalculator(calculator: 'dummyTaxCalculator')]
class DummyTaxCalculator implements CalculatorInterface
{
    public function calculate(float $base, TaxRateInterface $rate): float
    {
        return 16.0;
    }
}
