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

namespace Sylius\Bundle\PaymentBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PaymentBundle\DependencyInjection\SyliusPaymentExtension;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Payment\Attribute\AsPaymentMethodResolver;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class SyliusPaymentExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_payment_method_resolvers_with_attribute(): void
    {
        $this->container->register(
            'acme.payment_method_resolver_autoconfigured',
            DummyPaymentMethodResolver::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_payment_method_resolver_autoconfigured',
            PrioritizedDummyPaymentMethodResolver ::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.payment_method_resolver_autoconfigured',
            'sylius.payment_method_resolver',
            ['type' => 'dummy', 'label' => 'dummy', 'priority' => 0]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_payment_method_resolver_autoconfigured',
            'sylius.payment_method_resolver',
            ['type' => 'dummy', 'label' => 'dummy', 'priority' => 32]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusPaymentExtension()];
    }
}

#[AsPaymentMethodResolver(type: 'dummy', label: 'dummy')]
class DummyPaymentMethodResolver implements PaymentMethodsResolverInterface
{
    public function getSupportedMethods(PaymentInterface $subject): array
    {
        return [new PaymentMethod()];
    }

    public function supports(PaymentInterface $subject): bool
    {
        return true;
    }
}

#[AsPaymentMethodResolver(type: 'dummy', label: 'dummy', priority: 32)]
class PrioritizedDummyPaymentMethodResolver implements PaymentMethodsResolverInterface
{
    public function getSupportedMethods(PaymentInterface $subject): array
    {
        return [new PaymentMethod()];
    }

    public function supports(PaymentInterface $subject): bool
    {
        return true;
    }
}
