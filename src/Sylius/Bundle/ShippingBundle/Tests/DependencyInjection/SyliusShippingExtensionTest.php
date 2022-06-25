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

namespace Sylius\Bundle\ShippingBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ShippingBundle\DependencyInjection\SyliusShippingExtension;
use Sylius\Component\Shipping\Attribute\AsShippingCalculator;
use Sylius\Component\Shipping\Attribute\AsShippingMethodResolver;
use Sylius\Component\Shipping\Attribute\AsShippingMethodRuleChecker;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class SyliusShippingExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_shipping_calculator_with_attribute(): void
    {
        $this->container->register(
            'acme.shipping_calculator_autoconfigured',
            DummyShippingCalculator::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.shipping_calculator_autoconfigured',
            'sylius.shipping_calculator',
            [
                'calculator' => 'dummyShippingCalculator',
                'label' => 'Dummy Shipping Calculator',
                'formType' => 'DummyShippingCalculatorType'
            ]
        );
    }

    /** @test */
    public function it_autoconfigures_shipping_method_resolver_with_attribute(): void
    {
        $this->container->register(
            'acme.shipping_method_resolver_autoconfigured',
            DummyShippingMethodResolver::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_shipping_method_resolver_autoconfigured',
            PrioritizedDummyShippingMethodResolver::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.shipping_method_resolver_autoconfigured',
            'sylius.shipping_method_resolver',
            [
                'type' => 'dummyShippingMethodResolver',
                'label' => 'Dummy Shipping Method Resolver',
                'priority' => 0
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_shipping_method_resolver_autoconfigured',
            'sylius.shipping_method_resolver',
            [
                'type' => 'dummyShippingMethodResolver',
                'label' => 'Dummy Shipping Method Resolver',
                'priority' => 16
            ]
        );
    }

    /** @test */
    public function it_autoconfigures_shipping_method_rule_checker_with_attribute(): void
    {
        $this->container->register(
            'acme._shipping_method_rule_checker_autoconfigured',
            DummyShippingMethodRuleChecker::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme._shipping_method_rule_checker_autoconfigured',
            'sylius.shipping_method_rule_checker',
            [
                'type' => 'dummyShippingMethodRuleChecker',
                'label' => 'Dummy Shipping Method Rule Checker',
                'formType' => 'DummyShippingMethodRuleCheckerType'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusShippingExtension()];
    }

}

#[AsShippingCalculator(
    calculator: 'dummyShippingCalculator',
    label: 'Dummy Shipping Calculator',
    formType: 'DummyShippingCalculatorType'
)]
class DummyShippingCalculator implements CalculatorInterface
{
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        return 0;
    }

    public function getType(): string
    {
        return 'dummy_shipping_calculator';
    }
}

#[AsShippingMethodResolver(
    type: 'dummyShippingMethodResolver',
    label: 'Dummy Shipping Method Resolver'
)]
class DummyShippingMethodResolver implements ShippingMethodsResolverInterface
{
    public function getSupportedMethods(ShippingSubjectInterface $subject): array
    {
        return [];
    }

    public function supports(ShippingSubjectInterface $subject): bool
    {
        return true;
    }
}

#[AsShippingMethodResolver(
    type: 'dummyShippingMethodResolver',
    label: 'Dummy Shipping Method Resolver',
    priority: 16
)]
class PrioritizedDummyShippingMethodResolver implements ShippingMethodsResolverInterface
{
    public function getSupportedMethods(ShippingSubjectInterface $subject): array
    {
        return [];
    }

    public function supports(ShippingSubjectInterface $subject): bool
    {
        return true;
    }
}

#[AsShippingMethodRuleChecker(
    type: 'dummyShippingMethodRuleChecker',
    label: 'Dummy Shipping Method Rule Checker',
    formType: 'DummyShippingMethodRuleCheckerType'
)]
class DummyShippingMethodRuleChecker implements RuleCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $subject, array $configuration): bool
    {
        return true;
    }
}
