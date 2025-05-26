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

namespace Tests\Sylius\Bundle\ShippingBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingCalculator;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodResolver;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodRuleChecker;
use Sylius\Bundle\ShippingBundle\DependencyInjection\SyliusShippingExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\ShippingBundle\Stub\ShippingCalculatorStub;
use Tests\Sylius\Bundle\ShippingBundle\Stub\ShippingMethodResolverStub;
use Tests\Sylius\Bundle\ShippingBundle\Stub\ShippingMethodRuleCheckerStub;

final class SyliusShippingExtensionTest extends AbstractExtensionTestCase
{
    #[Test]
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
                'form_type' => 'SomeFormType',
                'priority' => 0,
            ],
        );
    }

    #[Test]
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

    #[Test]
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
                'form_type' => 'SomeFormType',
                'priority' => 20,
            ],
        );
    }

    #[Test]
    public function it_loads_shipping_method_rules_validation_groups_parameter_value_properly(): void
    {
        $this->load(['shipping_method_rule' => [
            'validation_groups' => [
                'total_weight_greater_than_or_equal' => ['sylius', 'sylius_shipping_method_rule_total_weight'],
                'order_total_greater_than_or_equal' => ['sylius'],
                'order_total_less_than_or_equal' => ['sylius', 'sylius_shipping_method_rule_order_total'],
                'total_weight_less_than_or_equal' => ['sylius'],
            ],
        ]]);

        $this->assertContainerBuilderHasParameter('sylius.shipping.shipping_method_rule.validation_groups', [
            'total_weight_greater_than_or_equal' => ['sylius', 'sylius_shipping_method_rule_total_weight'],
            'order_total_greater_than_or_equal' => ['sylius'],
            'order_total_less_than_or_equal' => ['sylius', 'sylius_shipping_method_rule_order_total'],
            'total_weight_less_than_or_equal' => ['sylius'],
        ]);
    }

    #[Test]
    public function it_loads_shipping_method_calculators_validation_groups_parameter_value_properly(): void
    {
        $this->load(['shipping_method_calculator' => [
            'validation_groups' => [
                'flat_rate' => ['sylius'],
                'per_unit_rate' => ['sylius', 'sylius_per_unit_rate'],
            ],
        ]]);

        $this->assertContainerBuilderHasParameter('sylius.shipping.shipping_method_calculator.validation_groups', [
            'flat_rate' => ['sylius'],
            'per_unit_rate' => ['sylius', 'sylius_per_unit_rate'],
        ]);
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusShippingExtension()];
    }
}
