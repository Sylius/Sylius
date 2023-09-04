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

namespace Sylius\Bundle\ShippingBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingCalculator;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodResolver;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodRuleChecker;
use Sylius\Bundle\ShippingBundle\DependencyInjection\SyliusShippingExtension;
use Sylius\Bundle\ShippingBundle\Tests\Stub\ShippingCalculatorStub;
use Sylius\Bundle\ShippingBundle\Tests\Stub\ShippingMethodResolverStub;
use Sylius\Bundle\ShippingBundle\Tests\Stub\ShippingMethodRuleCheckerStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusShippingExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_shipping_calculator_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.shipping_calculator_autoconfigured',
            (new Definition())
                ->setClass(ShippingCalculatorStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.shipping_calculator_autoconfigured',
            AsShippingCalculator::SERVICE_TAG,
            [
                'calculator' => 'test',
                'label' => 'Test',
                'form-type' => 'SomeFormType',
                'priority' => 0,
            ],
        );
    }

    /** @test */
    public function it_autoconfigures_shipping_method_resolver_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.shipping_method_resolver_autoconfigured',
            (new Definition())
                ->setClass(ShippingMethodResolverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.shipping_method_resolver_autoconfigured',
            AsShippingMethodResolver::SERVICE_TAG,
            [
                'type' => 'test',
                'label' => 'Test',
                'priority' => 10,
            ],
        );
    }

    /** @test */
    public function it_autoconfigures_shipping_method_rule_checker_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.shipping_method_rule_checker_autoconfigured',
            (new Definition())
                ->setClass(ShippingMethodRuleCheckerStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.shipping_method_rule_checker_autoconfigured',
            AsShippingMethodRuleChecker::SERVICE_TAG,
            [
                'type' => 'test',
                'label' => 'Test',
                'form-type' => 'SomeFormType',
                'priority' => 20,
            ],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusShippingExtension()];
    }
}
