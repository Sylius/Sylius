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

namespace Tests\Sylius\Bundle\TaxationBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\TaxationBundle\Attribute\AsTaxCalculator;
use Sylius\Bundle\TaxationBundle\DependencyInjection\SyliusTaxationExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\TaxationBundle\Stub\TaxCalculatorStub;

final class SyliusTaxationExtensionTest extends AbstractExtensionTestCase
{
    #[Test]
    public function it_autoconfigures_tax_calculator_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.tax_calculator_autoconfigured',
            (new Definition())
                ->setClass(TaxCalculatorStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.tax_calculator_autoconfigured',
            AsTaxCalculator::SERVICE_TAG,
            [
                'calculator' => 'test',
                'priority' => 0,
            ],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusTaxationExtension()];
    }
}
